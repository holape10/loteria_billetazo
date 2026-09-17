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
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

        public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }
}