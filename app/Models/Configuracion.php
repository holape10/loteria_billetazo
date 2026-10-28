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

    // Cuántas jugadas gratis recibe quien se registra (0 = no se regala nada)
    public static function jugadasGratisRegistro(): int
    {
        return max(0, (int) static::obtener(self::JUGADAS_GRATIS_REGISTRO, 0));
    }
}
