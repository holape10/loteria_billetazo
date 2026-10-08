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
        'numeros_en_vivo',
        'en_vivo_desde',
        'cerrado_en',
    ];


    protected $casts = [
        'fecha' => 'date',
        'numeros_en_vivo' => 'array',
        'en_vivo_desde' => 'datetime',
        'cerrado_en' => 'datetime',
    ];

        public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }

        public function fechaHora(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora);
    }

    // Las horas de los sorteos se ingresan en hora de Perú
    public function fechaHoraLima(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora, 'America/Lima');
    }

    // Se deja de vender en cuanto llega la hora del sorteo, para que nadie compre conociendo números ya extraídos
    public function ventasAbiertas(): bool
    {
        return $this->estado === 'pendiente' && ! $this->estaEnVivo() && $this->fechaHoraLima()->isFuture();
    }

    public function estaEnVivo(): bool
    {
        return $this->estado === 'pendiente' && ! empty($this->numeros_en_vivo);
    }

    // Números ganadores en el orden en que salieron (si se jugó en vivo) o los oficiales ordenados
    public function numerosExtraidos(): array
    {
        if (! empty($this->numeros_en_vivo)) {
            return array_map('intval', $this->numeros_en_vivo);
        }

        return array_values(array_filter([
            $this->numero_1, $this->numero_2, $this->numero_3,
            $this->numero_4, $this->numero_5, $this->numero_6,
        ]));
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