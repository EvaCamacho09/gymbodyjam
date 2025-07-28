<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('cedula', 'LIKE', "%{$search}%")
                    ->orWhere('correo', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtros de membresía
        if ($request->filled('membresia')) {
            switch ($request->membresia) {
                case 'moroso':
                    $query->whereDoesntHave('membresias', function ($q) {
                        $q->where('fecha_vencimiento', '>=', now());
                    });
                    Log::debug($query->get());
                    break;

                case 'proximos':
                    $fechaLimite = now()->addDays(7);
                    $query->whereHas('membresias', function ($q) use ($fechaLimite) {
                        $q->where('fecha_vencimiento', '>=', now())
                            ->where('fecha_vencimiento', '<=', $fechaLimite);
                    });
                    break;

                case 'activa':
                    $query->whereHas('membresias', function ($q) {
                        $q->where('fecha_vencimiento', '>', now());
                    });
                    break;

                case 'sin_membresia':
                    $query->whereDoesntHave('membresias');
                    break;
            }
        }

        

        $clientes = $query->with('membresias')->paginate(15);

        // Agregar información adicional a cada cliente
        foreach ($clientes->items() as $cliente) {
            $cliente->dias_restantes = $cliente->diasRestantes();
            $cliente->es_moroso = $cliente->esMoroso();
        }

        return response()->json($clientes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|unique:clientes,cedula',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo',

            // Nuevos campos de medidas corporales
            'peso' => 'nullable|numeric|min:0',
            'altura' => 'nullable|numeric|min:0',
            'porcentaje_grasa' => 'nullable|numeric|min:0|max:100',
            'masa_muscular' => 'nullable|numeric|min:0',
            'cintura' => 'nullable|numeric|min:0',
            'cadera' => 'nullable|numeric|min:0',
            'pecho_torax' => 'nullable|numeric|min:0',
            'biceps_relajado' => 'nullable|numeric|min:0',
            'biceps_contraido' => 'nullable|numeric|min:0',
            'antebrazo' => 'nullable|numeric|min:0',
            'muslo' => 'nullable|numeric|min:0',
            'pantorrilla' => 'nullable|numeric|min:0',
            'frecuencia_cardiaca' => 'nullable|integer|min:0',
            'presion_arterial' => 'nullable|string|max:10',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $cliente = Cliente::create($request->all());

        return response()->json([
            'cliente' => $cliente,
            'message' => 'Cliente creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load([
            'membresias',
            'asistencias' => function ($query) {
                $query->orderBy('fecha_ingreso', 'desc')->limit(10);
            }
        ]);

        $membresiaActiva = $cliente->membresiaActiva();

        return response()->json([
            'cliente' => $cliente,
            'membresia_activa' => $membresiaActiva ? [
                'id' => $membresiaActiva->id,
                'cliente_membresia_id' => $membresiaActiva->pivot->id,
                'nombre' => $membresiaActiva->nombre,
                'precio' => $membresiaActiva->precio,
                'duracion_dias' => $membresiaActiva->duracion_dias,
                'fecha_inicio' => $membresiaActiva->pivot->fecha_inicio,
                'fecha_vencimiento' => $membresiaActiva->pivot->fecha_vencimiento,
                'precio_pagado' => $membresiaActiva->pivot->precio_pagado,
                'estado_pago' => $membresiaActiva->pivot->estado_pago,
                'dias_restantes' => $cliente->diasRestantes(),
                'esta_vencida' => $cliente->esMoroso(),
                'proxima_a_vencer' => $cliente->diasRestantes() <= 7 && $cliente->diasRestantes() > 0
            ] : null,
            'estadisticas' => [
                'total_asistencias' => $cliente->totalAsistencias(),
                'asistencias_mes' => $cliente->asistenciasDelMes()->count(),
                'ya_ingreso_hoy' => $cliente->yaIngresoHoy(),
                'es_moroso' => $cliente->esMoroso()
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => [
                'required',
                'string',
                Rule::unique('clientes')->ignore($cliente->id)
            ],
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cliente->update($request->all());

        return response()->json([
            'cliente' => $cliente,
            'message' => 'Cliente actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado exitosamente'
        ]);
    }

    /**
     * Get clientes morosos
     */
    public function morosos()
    {
        $clientes = Cliente::whereHas('membresias', function ($query) {
            $query->where('fecha_vencimiento', '<', now());
        })->with('membresias')->get();

        return response()->json($clientes);
    }

    /**
     * Get clientes con membresía próxima a vencer
     */
    public function proximosVencer()
    {
        $fechaLimite = now()->addDays(5);

        $clientes = Cliente::whereHas('membresias', function ($query) use ($fechaLimite) {
            $query->where('fecha_vencimiento', '>=', now())
                ->where('fecha_vencimiento', '<=', $fechaLimite);
        })->with('membresias')->get();

        return response()->json($clientes);
    }

    /**
     * Generar enlace público para el cliente
     */
    public function generarEnlacePublico(Cliente $cliente)
    {
        $token = $cliente->obtenerToken();
        $url = $cliente->urlPublica();

        return response()->json([
            'token' => $token,
            'url' => $url,
            'message' => 'Enlace público generado exitosamente'
        ]);
    }
}
