<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $table = 'boletos';

    protected $fillable = [
        'cliente_id',
        'sorteo_id',
        'compra_id',
        'numero_1',
        'numero_2',
        'numero_3',
        'numero_4',
        'numero_5',
        'numero_6',
        'monto',
        'aciertos',
        'premio_ganado',
        'jugada_gratis_ganada',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
    protected $casts = [
        'jugada_gratis_ganada' => 'boolean',
    ];
}