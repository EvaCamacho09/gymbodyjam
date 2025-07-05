<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ClienteMembresia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Obtener estadísticas del dashboard
     */
    public function estadisticas()
    {
        // Total de clientes activos
        $totalClientes = Cliente::where('estado', 'activo')->count();

        // Clientes morosos (con membresía vencida)
        $clientesMorosos = Cliente::whereHas('membresias', function ($query) {
            $query->where('fecha_vencimiento', '<', now())
                  ->where('activa', true);
        })->count();

        // Clientes próximos a vencer (próximos 5 días)
        $fechaLimite = now()->addDays(5);
        $clientesProximosVencer = Cliente::whereHas('membresias', function ($query) use ($fechaLimite) {
            $query->where('fecha_vencimiento', '>=', now())
                  ->where('fecha_vencimiento', '<=', $fechaLimite)
                  ->where('activa', true);
        })->count();

        // Ingresos del mes actual
        $ingresosMes = ClienteMembresia::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('estado_pago', 'pagado')
            ->sum('precio_pagado');

        // Membresías con conteo de clientes activos
        $membresias = Membresia::where('activa', true)
            ->withCount('clientes')
            ->orderBy('clientes_count', 'desc')
            ->get();

        return response()->json([
            'total_clientes' => $totalClientes,
            'clientes_morosos' => $clientesMorosos,
            'clientes_proximos_vencer' => $clientesProximosVencer,
            'ingresos_mes' => $ingresosMes,
            'membresias' => $membresias,
        ]);
    }

    /**
     * Obtener actividad reciente
     */
    public function actividadReciente()
    {
        // Asistencias de hoy (necesitamos tener el modelo Asistencia)
        $asistenciasHoy = 0;
        try {
            if (class_exists(\App\Models\Asistencia::class)) {
                $asistenciasHoy = \App\Models\Asistencia::whereDate('created_at', now()->toDateString())->count();
            }
        } catch (\Exception $e) {
            // Si no existe el modelo o tabla, mantenemos 0
        }

        // Últimos clientes registrados
        $ultimosClientes = Cliente::latest()
            ->limit(5)
            ->get();

        // Últimas membresías asignadas
        $ultimasMembresiasAsignadas = ClienteMembresia::with(['cliente', 'membresia'])
            ->latest()
            ->limit(3)
            ->get();

        return response()->json([
            'asistencias_hoy' => $asistenciasHoy,
            'ultimos_clientes' => $ultimosClientes,
            'ultimas_membresias_asignadas' => $ultimasMembresiasAsignadas,
        ]);
    }
}
