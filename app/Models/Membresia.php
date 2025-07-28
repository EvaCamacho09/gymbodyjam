<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membresia extends Model
{
    use HasFactory, SoftDeletes;

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
