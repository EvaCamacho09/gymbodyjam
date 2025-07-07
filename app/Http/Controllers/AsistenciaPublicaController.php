<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Asistencia;
use App\Models\ClienteMembresia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaPublicaController extends Controller
{
    /**
     * Buscar cliente por nombre o cédula
     */
    public function buscarCliente(Request $request)
    {
        $request->validate([
            'busqueda' => 'required|string|min:2'
        ]);

        $busqueda = $request->input('busqueda');

        // Buscar por cédula exacta o nombre que contenga el término
        $clientes = Cliente::where('estado', 'activo')
            ->where(function($query) use ($busqueda) {
                $query->where('cedula', $busqueda)
                      ->orWhere('nombre', 'LIKE', "%{$busqueda}%");
            })
            ->with(['membresias' => function($query) {
                $query->where('activa', true)
                      ->orderBy('fecha_vencimiento', 'desc');
            }])
            ->limit(10)
            ->get();

        // Agregar información de membresía actual
        $clientes = $clientes->map(function($cliente) {
            $membresiaActual = $cliente->membresias->first();
            
            if ($membresiaActual) {
                $fechaVencimiento = Carbon::parse($membresiaActual->pivot->fecha_vencimiento);
                $hoy = Carbon::now();
                
                $cliente->membresia_actual = [
                    'nombre' => $membresiaActual->nombre,
                    'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
                    'dias_restantes' => $hoy->diffInDays($fechaVencimiento, false),
                    'vencida' => $fechaVencimiento->isPast(),
                    'estado' => $fechaVencimiento->isPast() ? 'vencida' : ($hoy->diffInDays($fechaVencimiento) <= 5 ? 'por_vencer' : 'activa')
                ];
            } else {
                $cliente->membresia_actual = null;
            }

            return $cliente;
        });

        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    }

    /**
     * Registrar asistencia
     */
    public function registrarAsistencia(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        $cliente = Cliente::with(['membresias' => function($query) {
            $query->where('activa', true)
                  ->orderBy('fecha_vencimiento', 'desc');
        }])->findOrFail($request->cliente_id);

        // Verificar si el cliente tiene membresía activa
        $membresiaActual = $cliente->membresias->first();
        
        if (!$membresiaActual) {
            return response()->json([
                'success' => false,
                'message' => 'El cliente no tiene membresía activa, hable con el administrador para activar su membresia'
            ], 400);
        }

        // Verificar si ya registró asistencia hoy
        $hoy = Carbon::now()->toDateString();
        $asistenciaHoy = Asistencia::where('cliente_id', $cliente->id)
            ->whereDate('created_at', $hoy)
            ->first();

        if ($asistenciaHoy) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has registrado asistencia hoy'
            ], 400);
        }

        // Registrar asistencia
        $asistencia = Asistencia::create([
            'cliente_id' => $cliente->id,
            'fecha_ingreso' => $hoy,
            'observaciones' => 'Registro automático desde monitor'
        ]);

        // Calcular días restantes
        $fechaVencimiento = Carbon::parse($membresiaActual->pivot->fecha_vencimiento);
        $diasRestantes = Carbon::now()->diffInDays($fechaVencimiento, false);
        $vencida = $fechaVencimiento->isPast();

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada con éxito',
            'cliente' => [
                'nombre' => $cliente->nombre,
                'cedula' => $cliente->cedula
            ],
            'membresia' => [
                'nombre' => $membresiaActual->nombre,
                'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y'),
                'dias_restantes' => $diasRestantes,
                'vencida' => $vencida,
                'estado' => $vencida ? 'vencida' : ($diasRestantes <= 5 ? 'por_vencer' : 'activa')
            ]
        ]);
    }
}
