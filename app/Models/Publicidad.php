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
        'ubicacion',
        'texto',
        'enlace',
        'imagen',
        'ajuste_imagen',
        'posicion_imagen',
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

    public const UBICACIONES = [
        'sorteo' => '📺 Sorteo en vivo',
        'inicio' => '🏠 Página de inicio',
        'ambos' => '📺🏠 Ambos',
    ];

    public const AJUSTES = [
        'contain' => 'Mostrar la imagen completa',
        'cover' => 'Llenar el recuadro (recorta los bordes)',
    ];

    public const POSICIONES = [
        'center' => 'Centro',
        'top' => 'Arriba',
        'bottom' => 'Abajo',
        'left' => 'Izquierda',
        'right' => 'Derecha',
    ];

    private const CACHE_SORTEO = 'publicidades:sorteo';
    private const CACHE_INICIO = 'publicidades:inicio';

    protected static function booted(): void
    {
        // Cualquier cambio del administrador se refleja enseguida en el sorteo y en la portada
        $limpiar = function () {
            Cache::forget(self::CACHE_SORTEO);
            Cache::forget(self::CACHE_INICIO);
        };
        static::saved($limpiar);
        static::deleted($limpiar);
    }

    // Publicidades activas para la pantalla del sorteo en vivo (se guardan 10 minutos en caché)
    public static function paraSorteo(): array
    {
        return Cache::remember(self::CACHE_SORTEO, now()->addMinutes(10), fn () => static::activas()
            ->whereIn('ubicacion', ['sorteo', 'ambos'])->get()->map->paraPantalla()->values()->all());
    }

    // Publicidades activas para la ventana y la sección de auspiciadores de la página de inicio
    public static function paraInicio(): array
    {
        return Cache::remember(self::CACHE_INICIO, now()->addMinutes(10), fn () => static::activas()
            ->whereIn('ubicacion', ['inicio', 'ambos'])->get()->map->paraPantalla()->values()->all());
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('id');
    }

    public function urlImagen(): ?string
    {
        return $this->imagen ? Storage::disk('public')->url($this->imagen) : null;
    }

    // Estilo para "acomodar" la imagen dentro del recuadro (completa o recortada, y desde qué lado)
    public function estiloImagen(): string
    {
        $ajuste = array_key_exists($this->ajuste_imagen, self::AJUSTES) ? $this->ajuste_imagen : 'contain';
        $posicion = array_key_exists($this->posicion_imagen, self::POSICIONES) ? $this->posicion_imagen : 'center';

        return "object-fit: {$ajuste}; object-position: {$posicion};";
    }

    // Datos que necesita la pantalla para mostrarla (y leerla en voz alta si corresponde)
    public function paraPantalla(): array
    {
        return [
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'enlace' => $this->enlace,
            'imagen' => $this->urlImagen(),
            'estilo' => $this->estiloImagen(),
            'audio' => $this->audio ? Storage::disk('public')->url($this->audio) : null,
            'voz' => $this->con_voz ? ($this->texto_voz ?: trim($this->titulo . '. ' . $this->texto)) : null,
            'segundos' => $this->duracion_segundos,
        ];
    }
}
