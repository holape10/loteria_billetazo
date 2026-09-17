<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'cliente_id',
        'sorteo_id',
        'cantidad_jugadas',
        'monto_total',
        'metodo_pago',
        'numero_operacion',
        'comprobante',
        'estado_pago',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }
}