<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Services\ComprobantePagoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ComprobanteController extends Controller
{
    private function autorizar(Compra $compra): void
    {
        $usuario = Auth::user();

        if (! $usuario->esAdministrador() && (int) $compra->cliente_id !== (int) $usuario->cliente_id) {
            abort(403, 'No tienes permiso para ver este comprobante.');
        }
    }

    public function ver(Compra $compra)
    {
        $this->autorizar($compra);

        $compra->load(['cliente', 'sorteo', 'boletos']);

        $qr = base64_encode(QrCode::format('svg')->size(180)->generate(route('compras.comprobante', $compra)));

        return view('empresa.compras.comprobante', compact('compra', 'qr'));
    }

    public function pdf(Compra $compra)
    {
        $this->autorizar($compra);

        $compra->load(['cliente', 'sorteo', 'boletos']);

        $qr = base64_encode(QrCode::format('svg')->size(160)->generate(route('compras.comprobante', $compra)));

        $pdf = Pdf::loadView('empresa.compras.comprobante-pdf', compact('compra', 'qr'));

        return $pdf->download('comprobante-billetazo-' . $compra->id . '.pdf');
    }

    public function imagen(Compra $compra, ComprobantePagoService $comprobantes)
    {
        $this->autorizar($compra);

        $disco = $compra->comprobante ? $comprobantes->discoDe($compra->comprobante) : null;

        abort_unless($disco, 404);

        return Storage::disk($disco)->response($compra->comprobante, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function subir(Request $request, Compra $compra, ComprobantePagoService $comprobantes)
    {
        $usuario = Auth::user();

        // Solo el dueño de la compra puede adjuntar su comprobante
        abort_unless($usuario->cliente_id && (int) $compra->cliente_id === (int) $usuario->cliente_id, 403);

        if ($compra->estado_pago !== 'pendiente' || $compra->monto_total <= 0) {
            return back()->with('error', 'Solo puedes subir el comprobante de compras pendientes de validación.');
        }

        $request->validate([
            'comprobante' => array_merge(['required'], ComprobantePagoService::REGLAS),
        ], [
            'comprobante.required' => 'Selecciona la captura de tu pago.',
            'comprobante.image' => 'El archivo debe ser una imagen.',
            'comprobante.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'comprobante.max' => 'La imagen no puede pesar más de 4 MB.',
        ]);

        $anterior = $compra->comprobante;

        // Si reemplaza una captura, el número de operación se vuelve a leer de la nueva imagen
        $datos = $comprobantes->guardar(
            $request->file('comprobante'),
            (float) $compra->monto_total,
            $anterior ? null : $compra->numero_operacion,
            $compra->id
        );
        $datos['numero_operacion'] ??= $compra->numero_operacion;

        $compra->update($datos);

        if ($anterior && $anterior !== $compra->comprobante) {
            $comprobantes->eliminar($anterior);
        }

        return back()->with('exito', 'Comprobante enviado correctamente. Validaremos tu pago en breve.');
    }
}
