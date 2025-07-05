<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membresia;
use App\Models\Cliente;
use App\Models\ClienteMembresia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MembresiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $membresias = Membresia::where('activa', true)->get();
        return response()->json($membresias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'duracion_dias' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean',
        ]);

        $membresia = Membresia::create($request->all());

        return response()->json([
            'membresia' => $membresia,
            'message' => 'Membresía creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Membresia $membresia)
    {
        $membresia->load('clientes');
        return response()->json($membresia);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membresia $membresia)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'duracion_dias' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean',
        ]);

        $membresia->update($request->all());

        return response()->json([
            'membresia' => $membresia,
            'message' => 'Membresía actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membresia $membresia)
    {
        $membresia->update(['activa' => false]);

        return response()->json([
            'message' => 'Membresía desactivada exitosamente'
        ]);
    }

    /**
     * Asignar membresía a cliente
     */
    public function asignarCliente(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'membresia_id' => 'required|exists:membresias,id',
            'fecha_inicio' => 'required|date',
            'precio_pagado' => 'required|numeric|min:0',
            'estado_pago' => 'nullable|in:pendiente,pagado',
        ]);

        $membresia = Membresia::findOrFail($request->membresia_id);
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaVencimiento = $fechaInicio->copy()->addDays($membresia->duracion_dias);

        $clienteMembresia = ClienteMembresia::create([
            'cliente_id' => $request->cliente_id,
            'membresia_id' => $request->membresia_id,
            'fecha_inicio' => $fechaInicio,
            'fecha_vencimiento' => $fechaVencimiento,
            'precio_pagado' => $request->precio_pagado,
            'estado_pago' => $request->estado_pago ?? 'pagado', // Valor por defecto
        ]);

        $clienteMembresia->load(['cliente', 'membresia']);

        return response()->json([
            'cliente_membresia' => $clienteMembresia,
            'message' => 'Membresía asignada exitosamente'
        ], 201);
    }

    /**
     * Renovar membresía de cliente
     */
    public function renovarMembresia(Request $request, $clienteMembresiaId)
    {
        $request->validate([
            'precio_pagado' => 'required|numeric|min:0',
            'estado_pago' => 'nullable|in:pendiente,pagado',
        ]);

        $clienteMembresia = ClienteMembresia::findOrFail($clienteMembresiaId);
        $membresia = $clienteMembresia->membresia;
        
        $nuevaFechaInicio = Carbon::now();
        $nuevaFechaVencimiento = $nuevaFechaInicio->copy()->addDays($membresia->duracion_dias);

        $nuevaClienteMembresia = ClienteMembresia::create([
            'cliente_id' => $clienteMembresia->cliente_id,
            'membresia_id' => $clienteMembresia->membresia_id,
            'fecha_inicio' => $nuevaFechaInicio,
            'fecha_vencimiento' => $nuevaFechaVencimiento,
            'precio_pagado' => $request->precio_pagado,
            'estado_pago' => $request->estado_pago ?? 'pagado', // Valor por defecto
        ]);

        $nuevaClienteMembresia->load(['cliente', 'membresia']);

        return response()->json([
            'cliente_membresia' => $nuevaClienteMembresia,
            'message' => 'Membresía renovada exitosamente'
        ], 201);
    }

    /**
     * Cambiar membresía de cliente
     */
    public function cambiarMembresia(Request $request, $clienteMembresiaId)
    {
        $request->validate([
            'nueva_membresia_id' => 'required|exists:membresias,id',
            'precio_pagado' => 'required|numeric|min:0',
            'estado_pago' => 'nullable|in:pendiente,pagado',
        ]);

        $clienteMembresiaAnterior = ClienteMembresia::findOrFail($clienteMembresiaId);
        $nuevaMembresia = Membresia::findOrFail($request->nueva_membresia_id);
        
        $nuevaFechaInicio = Carbon::now();
        $nuevaFechaVencimiento = $nuevaFechaInicio->copy()->addDays($nuevaMembresia->duracion_dias);

        $nuevaClienteMembresia = ClienteMembresia::create([
            'cliente_id' => $clienteMembresiaAnterior->cliente_id,
            'membresia_id' => $request->nueva_membresia_id,
            'fecha_inicio' => $nuevaFechaInicio,
            'fecha_vencimiento' => $nuevaFechaVencimiento,
            'precio_pagado' => $request->precio_pagado,
            'estado_pago' => $request->estado_pago ?? 'pagado',
        ]);

        $nuevaClienteMembresia->load(['cliente', 'membresia']);

        return response()->json([
            'cliente_membresia' => $nuevaClienteMembresia,
            'message' => 'Membresía cambiada exitosamente'
        ], 201);
    }

    /**
     * Obtener historial de membresías de un cliente
     */
    public function historialMembresias($clienteId)
    {
        $historial = ClienteMembresia::with('membresia')
            ->where('cliente_id', $clienteId)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json($historial);
    }
}
