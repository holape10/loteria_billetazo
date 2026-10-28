<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Publicidad extends Model
{
    protected $table = 'publicidades';

    protected $fillable = [
        'titulo',
        'texto',
        'imagen',
        'audio',
        'texto_voz',
        'con_voz',
        'activo',
        'orden',
        'duracion_segundos',
    ];

    protected $casts = [
        'con_voz' => 'boolean',
        'activo' => 'boolean',
    ];

    public const CLAVE_CACHE = 'publicidades:sorteo';

    protected static function booted(): void
    {
        // Cualquier cambio del administrador se refleja enseguida en el sorteo
        $limpiar = fn () => Cache::forget(self::CLAVE_CACHE);
        static::saved($limpiar);
        static::deleted($limpiar);
    }

    // Publicidades activas listas para la pantalla del sorteo (se guardan 10 minutos en caché)
    public static function paraSorteo(): array
    {
        return Cache::remember(self::CLAVE_CACHE, now()->addMinutes(10), fn () => static::activas()->get()->map->paraPantalla()->values()->all());
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('id');
    }

    // Datos que necesita la pantalla del sorteo para mostrarla (y leerla en voz alta si corresponde)
    public function paraPantalla(): array
    {
        return [
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'imagen' => $this->imagen ? Storage::disk('public')->url($this->imagen) : null,
            'audio' => $this->audio ? Storage::disk('public')->url($this->audio) : null,
            'voz' => $this->con_voz ? ($this->texto_voz ?: trim($this->titulo . '. ' . $this->texto)) : null,
            'segundos' => $this->duracion_segundos,
        ];
    }
}
