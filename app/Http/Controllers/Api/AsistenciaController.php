<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Listar asistencias con filtros
     */
    public function index(Request $request)
    {
        $query = Asistencia::with(['cliente', 'clienteMembresia.membresia'])
                          ->orderBy('fecha_ingreso', 'desc');

        // Filtros
        if ($request->has('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('fecha')) {
            $query->whereDate('fecha_ingreso', $request->fecha);
        }

        if ($request->has('hoy') && $request->hoy) {
            $query->whereDate('fecha_ingreso', Carbon::today());
        }

        if ($request->has('mes_actual') && $request->mes_actual) {
            $query->delMes();
        }

        $asistencias = $query->paginate(50);

        return response()->json($asistencias);
    }

    /**
     * Registrar ingreso de cliente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'permitir_sin_membresia' => 'boolean',
            'observaciones' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        
        // Verificar si ya registró ingreso hoy
        if ($cliente->yaIngresoHoy()) {
            return response()->json([
                'message' => 'El cliente ya registró su ingreso hoy',
                'ya_ingreso' => true
            ], 400);
        }

        try {
            $asistencia = $cliente->registrarIngreso(
                $request->permitir_sin_membresia ?? false,
                $request->observaciones
            );

            $asistencia->load(['cliente', 'clienteMembresia.membresia']);

            return response()->json([
                'message' => 'Ingreso registrado exitosamente',
                'asistencia' => $asistencia,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'dias_restantes' => $cliente->diasRestantes(),
                    'es_moroso' => $cliente->esMoroso(),
                    'total_asistencias' => $cliente->totalAsistencias()
                ]
            ], 201);
        } catch (\Exception $e) {
            // Si el error es por membresía inválida, proporcionar más información
            if (strpos($e->getMessage(), 'membresía válida') !== false) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'membresia_invalida' => true,
                    'requiere_permiso' => true
                ], 400);
            }
            
            return response()->json([
                'message' => $e->getMessage(),
                'membresia_invalida' => true
            ], 400);
        }
    }

    /**
     * Registrar ingreso por búsqueda rápida
     */
    public function registrarPorBusqueda(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'busqueda' => 'required|string|min:3',
            'permitir_sin_membresia' => 'boolean',
            'observaciones' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buscar cliente por nombre, cédula, correo o teléfono
        $clientes = Cliente::where('nombre', 'like', '%' . $request->busqueda . '%')
                          ->orWhere('cedula', 'like', '%' . $request->busqueda . '%')
                          ->orWhere('correo', 'like', '%' . $request->busqueda . '%')
                          ->orWhere('telefono', 'like', '%' . $request->busqueda . '%')
                          ->get();

        if ($clientes->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron clientes con esa búsqueda'
            ], 404);
        }

        if ($clientes->count() > 1) {
            return response()->json([
                'message' => 'Se encontraron múltiples clientes, seleccione uno específico',
                'clientes' => $clientes->map(function ($cliente) {
                    return [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono,
                        'dias_restantes' => $cliente->diasRestantes(),
                        'es_moroso' => $cliente->esMoroso()
                    ];
                })
            ], 300);
        }

        $cliente = $clientes->first();

        // Verificar si ya registró ingreso hoy
        if ($cliente->yaIngresoHoy()) {
            return response()->json([
                'message' => 'El cliente ya registró su ingreso hoy',
                'ya_ingreso' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'dias_restantes' => $cliente->diasRestantes()
                ]
            ], 400);
        }

        try {
            $asistencia = $cliente->registrarIngreso(
                $request->permitir_sin_membresia ?? false,
                $request->observaciones
            );

            $asistencia->load(['cliente', 'clienteMembresia.membresia']);

            return response()->json([
                'message' => 'Ingreso registrado exitosamente',
                'asistencia' => $asistencia,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'dias_restantes' => $cliente->diasRestantes(),
                    'es_moroso' => $cliente->esMoroso(),
                    'total_asistencias' => $cliente->totalAsistencias()
                ]
            ], 201);
        } catch (\Exception $e) {
            // Si el error es por membresía inválida, proporcionar más información
            if (strpos($e->getMessage(), 'membresía válida') !== false) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'membresia_invalida' => true,
                    'requiere_permiso' => true,
                    'cliente' => [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'dias_restantes' => $cliente->diasRestantes(),
                        'es_moroso' => $cliente->esMoroso()
                    ]
                ], 400);
            }
            
            return response()->json([
                'message' => $e->getMessage(),
                'membresia_invalida' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'dias_restantes' => $cliente->diasRestantes()
                ]
            ], 400);
        }
    }

    /**
     * Estadísticas de asistencias
     */
    public function estadisticas()
    {
        $hoy = Carbon::today();
        $inicioSemana = $hoy->copy()->startOfWeek();
        $inicioMes = $hoy->copy()->startOfMonth();

        return response()->json([
            'asistencias_hoy' => Asistencia::whereDate('fecha_ingreso', $hoy)->count(),
            'asistencias_semana' => Asistencia::whereBetween('fecha_ingreso', [$inicioSemana, $hoy->copy()->endOfDay()])->count(),
            'asistencias_mes' => Asistencia::whereBetween('fecha_ingreso', [$inicioMes, $hoy->copy()->endOfDay()])->count(),
            'promedio_diario_mes' => round(Asistencia::whereBetween('fecha_ingreso', [$inicioMes, $hoy->copy()->endOfDay()])->count() / $hoy->day, 1),
            'clientes_activos_hoy' => Asistencia::whereDate('fecha_ingreso', $hoy)->distinct('cliente_id')->count(),
        ]);
    }

    /**
     * Obtener asistencias de un cliente específico
     */
    public function show($id)
    {
        $asistencia = Asistencia::with(['cliente', 'clienteMembresia.membresia'])
                               ->findOrFail($id);

        return response()->json($asistencia);
    }

    /**
     * Eliminar asistencia (solo si fue registrada hoy)
     */
    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        
        // Solo permitir eliminar si fue registrada hoy
        if ($asistencia->fecha_ingreso->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return response()->json([
                'message' => 'Solo se pueden eliminar asistencias registradas hoy'
            ], 400);
        }

        $asistencia->delete();

        return response()->json([
            'message' => 'Asistencia eliminada exitosamente'
        ]);
    }
}
