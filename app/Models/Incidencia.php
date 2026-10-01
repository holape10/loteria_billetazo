<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'fecha_deposito',
        'observaciones',
        'leido',
    ];

    protected $casts = [
        'fecha_deposito' => 'date',
        'leido' => 'boolean',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}