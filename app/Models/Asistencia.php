<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'cliente_membresia_id',
        'fecha_ingreso',
        'membresia_valida',
        'observaciones'
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'membresia_valida' => 'boolean'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteMembresia()
    {
        return $this->belongsTo(ClienteMembresia::class);
    }

    // Scopes
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_ingreso', Carbon::today());
    }

    public function scopeDelMes($query)
    {
        return $query->whereMonth('fecha_ingreso', Carbon::now()->month)
                    ->whereYear('fecha_ingreso', Carbon::now()->year);
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    // Métodos auxiliares
    public function getFechaIngresoFormateadaAttribute()
    {
        return $this->fecha_ingreso->format('d/m/Y H:i');
    }
}
