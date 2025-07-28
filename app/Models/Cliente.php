<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula',
        'correo',
        'telefono',
        'estado',
        'token',
        'peso',
        'altura',
        'imc',
        'porcentaje_grasa',
        'masa_muscular',
        'cintura',
        'cadera',
        'pecho_torax',
        'biceps_relajado',
        'biceps_contraido',
        'antebrazo',
        'muslo',
        'pantorrilla',
        'frecuencia_cardiaca',
        'presion_arterial',
        'observaciones',
    ];

    /**
     * Relación con membresías
     */
    public function membresias()
    {
        return $this->belongsToMany(Membresia::class, 'cliente_membresia')
            ->withPivot('fecha_inicio', 'fecha_vencimiento', 'precio_pagado', 'estado_pago')
            ->withTimestamps();
    }

    /**
     * Obtener la membresía activa del cliente
     */
    public function membresiaActiva()
    {
        return $this->belongsToMany(Membresia::class, 'cliente_membresia')
            ->withPivot('fecha_inicio', 'fecha_vencimiento', 'precio_pagado', 'estado_pago', 'id')
            ->wherePivot('fecha_vencimiento', '>=', now())
            ->latest('pivot_fecha_inicio')
            ->first();
    }

public function ultimaMembresiaActiva()
{
    return $this->hasOne(ClienteMembresia::class, 'cliente_id')
        ->whereNull('deleted_at')
        ->orderByDesc('fecha_activacion'); // O `created_at` si no tienes esa columna
}

    /**
     * Verificar si el cliente está moroso
     */
    public function esMoroso(): bool
    {
        $membresiaActiva = $this->membresiaActiva();
        return !$membresiaActiva || $membresiaActiva->pivot->fecha_vencimiento < now();
    }

    /**
     * Obtener días restantes de membresía
     */
    public function diasRestantes(): ?int
    {
        $membresiaActiva = $this->membresiaActiva();
        if (!$membresiaActiva) {
            return null; // Sin membresía activa
        }

        $fechaVencimiento = $membresiaActiva->pivot->fecha_vencimiento;
        $hoy = now();

        // Si la fecha de vencimiento ya pasó, retornamos 0 o negativo
        $diff = $hoy->diffInDays($fechaVencimiento, false);

        return $diff >= 0 ? $diff + 1 : $diff;
    }

    /**
     * Relación con asistencias
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class)->orderBy('fecha_ingreso', 'desc');
    }

    /**
     * Asistencias del día de hoy
     */
    public function asistenciasHoy()
    {
        return $this->asistencias()->hoy();
    }

    /**
     * Asistencias del mes actual
     */
    public function asistenciasDelMes()
    {
        return $this->asistencias()->delMes();
    }

    /**
     * Verificar si ya se registró ingreso hoy
     */
    public function yaIngresoHoy(): bool
    {
        return $this->asistenciasHoy()->exists();
    }

    /**
     * Obtener total de asistencias
     */
    public function totalAsistencias(): int
    {
        return $this->asistencias()->count();
    }

    /**
     * Registrar ingreso/asistencia
     */
    public function registrarIngreso($permitirSinMembresia = false, $observaciones = null): Asistencia
    {
        $membresiaActiva = $this->membresiaActiva();
        $membresiaValida = $membresiaActiva && !$this->esMoroso();

        // Si no tiene membresía válida y no se permite sin membresía, lanzar excepción
        if (!$membresiaValida && !$permitirSinMembresia) {
            throw new \Exception('El cliente no tiene una membresía válida');
        }

        return $this->asistencias()->create([
            'cliente_membresia_id' => $membresiaActiva?->pivot->id,
            'fecha_ingreso' => now(),
            'membresia_valida' => $membresiaValida,
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Generar un token único para el cliente
     */
    public function generarToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (static::where('token', $token)->exists());

        $this->update(['token' => $token]);
        return $token;
    }

    /**
     * Obtener o generar token
     */
    public function obtenerToken(): string
    {
        if (!$this->token) {
            return $this->generarToken();
        }
        return $this->token;
    }

    /**
     * Generar URL pública para el cliente
     */
    public function urlPublica(): string
    {
        return url('/cliente/' . $this->obtenerToken());
    }

    public function calcularYGuardarImc(): void
    {
        if ($this->peso && $this->altura) {
            $alturaMetros = $this->altura / 100;

            if ($alturaMetros > 0) {
                $this->imc = round($this->peso / ($alturaMetros ** 2), 2);
                $this->save();
            }
        }
    }
}
