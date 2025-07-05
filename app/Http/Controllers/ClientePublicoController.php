<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClientePublicoController extends Controller
{
    /**
     * Mostrar información pública del cliente
     */
    public function mostrar($token)
    {
        $cliente = Cliente::where('token', $token)->first();
        
        if (!$cliente) {
            abort(404, 'Cliente no encontrado');
        }
        
        // Obtener membresía activa
        $membresiaActiva = $cliente->membresiaActiva();
        
        // Obtener historial de membresías
        $historialMembresias = $cliente->membresias()
            ->withPivot('fecha_inicio', 'fecha_vencimiento', 'precio_pagado', 'estado_pago')
            ->orderBy('cliente_membresia.fecha_inicio', 'desc')
            ->get();
        
        // Obtener asistencias recientes (últimas 30)
        $asistenciasRecientes = $cliente->asistencias()
            ->with('clienteMembresia.membresia')
            ->orderBy('fecha_ingreso', 'desc')
            ->take(30)
            ->get();
        
        // Estadísticas del mes actual
        $asistenciasEsteMes = $cliente->asistenciasDelMes()->count();
        $totalAsistencias = $cliente->totalAsistencias();
        
        $datos = [
            'cliente' => [
                'nombre' => $cliente->nombre,
                'cedula' => $cliente->cedula,
                'correo' => $cliente->correo,
                'telefono' => $cliente->telefono,
                'estado' => $cliente->estado
            ],
            'membresia_activa' => $membresiaActiva ? [
                'nombre' => $membresiaActiva->nombre,
                'descripcion' => $membresiaActiva->descripcion,
                'duracion_dias' => $membresiaActiva->duracion_dias,
                'fecha_inicio' => $membresiaActiva->pivot->fecha_inicio,
                'fecha_vencimiento' => $membresiaActiva->pivot->fecha_vencimiento,
                'precio_pagado' => $membresiaActiva->pivot->precio_pagado,
                'estado_pago' => $membresiaActiva->pivot->estado_pago,
                'dias_restantes' => $cliente->diasRestantes(),
                'esta_vencida' => $cliente->esMoroso()
            ] : null,
            'historial_membresias' => $historialMembresias,
            'asistencias_recientes' => $asistenciasRecientes,
            'estadisticas' => [
                'asistencias_este_mes' => $asistenciasEsteMes,
                'total_asistencias' => $totalAsistencias
            ]
        ];
        
        return view('cliente-publico', compact('datos'));
    }
}
