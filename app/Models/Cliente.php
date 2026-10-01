<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'dni',
        'direccion',
        'celular',
        'correo',
        'juegos',
        'estado',
        'jugadas_gratis',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }

    public function incidenciasNoLeidas(): int
    {
        return Incidencia::whereHas('compra', function ($q) {
            $q->where('cliente_id', $this->id);
        })->where('leido', false)->count();
    }
}