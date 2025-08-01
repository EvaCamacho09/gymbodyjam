<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClienteMembresia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cliente_membresia';

    protected $fillable = [
        'cliente_id',
        'membresia_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'precio_pagado',
        'estado_pago',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'precio_pagado' => 'decimal:2'
    ];

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con membresía
     */
    public function membresia()
    {
        return $this->belongsTo(Membresia::class);
    }

    /**
     * Relación con asistencias
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Verificar si la membresía está activa
     */
    public function estaActiva(): bool
    {
        return $this->fecha_vencimiento >= now()->toDateString();
    }

    /**
     * Verificar si la membresía está vencida
     */
    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento < now()->toDateString();
    }

    /**
     * Obtener días restantes
     */

    public function diasRestantes(): ?int
    {
        $membresiaActiva = $this->estaActiva;
        if (!$membresiaActiva) {
            return null; // Sin membresía activa
        }

        $fechaVencimiento = $this->fecha_vencimiento;
        $hoy = now();

        // Si la fecha de vencimiento ya pasó, retornamos 0 o negativo
        $diff = $hoy->diffInDays($fechaVencimiento, false);

        return $diff >= 0 ? $diff + 1 : $diff;
    }

    /**
     * Verificar si está próxima a vencer (7 días o menos)
     */
    public function proximaAVencer(): bool
    {
        return $this->estaActiva() && $this->diasRestantes() <= 7;
    }
}
