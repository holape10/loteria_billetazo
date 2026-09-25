<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ComprobanteController extends Controller
{
    private function autorizar(Compra $compra): void
    {
        $usuario = Auth::user();

        if (! $usuario->esAdministrador() && $compra->cliente_id !== $usuario->cliente_id) {
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
}