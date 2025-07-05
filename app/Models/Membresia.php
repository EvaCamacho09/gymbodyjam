<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'duracion_dias',
        'precio',
        'descripcion',
        'activa'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activa' => 'boolean'
    ];

    /**
     * Relación con clientes
     */
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_membresia')
                    ->withPivot('fecha_inicio', 'fecha_vencimiento', 'precio_pagado', 'estado_pago')
                    ->withTimestamps();
    }
}
