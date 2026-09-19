<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorteo extends Model
{
    use HasFactory;

    protected $table = 'sorteos';

    protected $fillable = [
        'fecha',
        'numero_1',
        'numero_2',
        'numero_3',
        'numero_4',
        'numero_5',
        'numero_6',
        'boletos_vendidos',
        'premio_mayor',
        'premio_cinco_aciertos',
        'premio_cuatro_aciertos',        
        'estado',
        'hora',
        'premio_tres_aciertos',
    ];


    protected $casts = [
        'fecha' => 'date',
    ];

        public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }

        public function fechaHora(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora);
    }

         public function ganadores()
    {
        return $this->boletos()
            ->where(function ($q) {
                $q->whereNotNull('premio_ganado')->orWhere('jugada_gratis_ganada', true);
            })
            ->with('cliente')
            ->orderByDesc('aciertos')
            ->get();
    }
}