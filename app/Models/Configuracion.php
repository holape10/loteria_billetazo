<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor'];

    public const JUGADAS_GRATIS_REGISTRO = 'jugadas_gratis_registro';

    public static function obtener(string $clave, mixed $porDefecto = null): mixed
    {
        return Cache::rememberForever("configuracion:{$clave}", fn () => static::where('clave', $clave)->value('valor')) ?? $porDefecto;
    }

    public static function guardar(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget("configuracion:{$clave}");
    }

    public const PUBLICIDAD_EN_VIVO_MODO = 'publicidad_en_vivo_modo';
    public const PUBLICIDAD_INICIO_FRECUENCIA = 'publicidad_inicio_frecuencia';

    // Cuándo ve la publicidad quien entra a la pantalla del sorteo en vivo
    public const MODOS_EN_VIVO = [
        'siempre' => 'Cada vez que entra (aunque el sorteo ya haya empezado)',
        'una_vez' => 'Una sola vez por sorteo (aunque salga y vuelva a entrar)',
        'antes_de_empezar' => 'Solo si entra antes de que salga el primer número',
        'nunca' => 'Nunca al entrar (solo cuando el admin presiona "Pasar publicidad")',
    ];

    // Cada cuánto aparece la ventana de publicidad en la página de inicio
    public const FRECUENCIAS_INICIO = [
        'siempre' => 'Cada vez que se abre la página',
        'sesion' => 'Una vez por visita (al volver a abrir el navegador o la app)',
        'dia' => 'Una vez al día',
        'nunca' => 'Nunca (solo se ven en la sección de auspiciadores)',
    ];

    public static function publicidadEnVivoModo(): string
    {
        $modo = static::obtener(self::PUBLICIDAD_EN_VIVO_MODO, 'siempre');

        return array_key_exists($modo, self::MODOS_EN_VIVO) ? $modo : 'siempre';
    }

    public static function publicidadInicioFrecuencia(): string
    {
        $frecuencia = static::obtener(self::PUBLICIDAD_INICIO_FRECUENCIA, 'sesion');

        return array_key_exists($frecuencia, self::FRECUENCIAS_INICIO) ? $frecuencia : 'sesion';
    }

    // Cuántas jugadas gratis recibe quien se registra (0 = no se regala nada)
    public static function jugadasGratisRegistro(): int
    {
        return max(0, (int) static::obtener(self::JUGADAS_GRATIS_REGISTRO, 0));
    }
}
