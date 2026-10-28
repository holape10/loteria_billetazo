<?php

namespace App\Http\Controllers;

use App\Models\Publicidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicidadController extends Controller
{
    public function index()
    {
        $publicidades = Publicidad::orderBy('orden')->orderBy('id')->get();

        return view('empresa.publicidades.index', compact('publicidades'));
    }

    public function create()
    {
        return view('empresa.publicidades.form', ['publicidad' => new Publicidad(['activo' => true, 'duracion_segundos' => 10])]);
    }

    public function store(Request $request)
    {
        $publicidad = new Publicidad();
        $this->guardar($request, $publicidad);

        return redirect()->route('publicidades.index')->with('exito', 'Publicidad creada correctamente.');
    }

    public function edit(Publicidad $publicidad)
    {
        return view('empresa.publicidades.form', compact('publicidad'));
    }

    public function update(Request $request, Publicidad $publicidad)
    {
        $this->guardar($request, $publicidad);

        return redirect()->route('publicidades.index')->with('exito', 'Publicidad actualizada correctamente.');
    }

    public function destroy(Publicidad $publicidad)
    {
        Storage::disk('public')->delete(array_filter([$publicidad->imagen, $publicidad->audio]));
        $publicidad->delete();

        return redirect()->route('publicidades.index')->with('exito', 'Publicidad eliminada.');
    }

    public function alternar(Publicidad $publicidad)
    {
        $publicidad->update(['activo' => ! $publicidad->activo]);

        return back()->with('exito', $publicidad->activo ? 'Publicidad activada.' : 'Publicidad desactivada.');
    }

    private function guardar(Request $request, Publicidad $publicidad): void
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:150',
            'texto' => 'nullable|string|max:1000',
            'texto_voz' => 'nullable|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'audio' => 'nullable|file|mimes:mp3,m4a,ogg,wav|max:5120',
            'duracion_segundos' => 'required|integer|min:3|max:60',
            'orden' => 'nullable|integer|min:0|max:999',
            'quitar_imagen' => 'nullable|boolean',
            'quitar_audio' => 'nullable|boolean',
        ], [
            'imagen.max' => 'La imagen no puede pesar más de 3 MB.',
            'audio.max' => 'El audio no puede pesar más de 5 MB.',
            'audio.mimes' => 'El audio debe ser MP3, M4A, OGG o WAV.',
        ]);

        $disco = Storage::disk('public');

        foreach (['imagen', 'audio'] as $campo) {
            $quitar = $request->boolean("quitar_{$campo}");

            if (($quitar || $request->hasFile($campo)) && $publicidad->$campo) {
                $disco->delete($publicidad->$campo);
                $publicidad->$campo = null;
            }

            if ($request->hasFile($campo)) {
                $publicidad->$campo = $request->file($campo)->store('publicidades', 'public');
            }
        }

        $publicidad->fill([
            'titulo' => $datos['titulo'],
            'texto' => $datos['texto'] ?? null,
            'texto_voz' => $datos['texto_voz'] ?? null,
            'duracion_segundos' => $datos['duracion_segundos'],
            'orden' => $datos['orden'] ?? 0,
            'con_voz' => $request->boolean('con_voz'),
            'activo' => $request->boolean('activo'),
        ])->save();
    }
}
