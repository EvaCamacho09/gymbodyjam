<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\MembresiaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AsistenciaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta de prueba para verificar conectividad
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working correctly',
        'timestamp' => now()->toISOString()
    ]);
});

// Rutas públicas de autenticación
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard/estadisticas', [DashboardController::class, 'estadisticas']);
    Route::get('/dashboard/actividad-reciente', [DashboardController::class, 'actividadReciente']);
    
    // Clientes
    Route::apiResource('clientes', ClienteController::class);
    Route::get('/clientes-morosos', [ClienteController::class, 'morosos']);
    Route::get('/clientes-proximos-vencer', [ClienteController::class, 'proximosVencer']);
    Route::post('/clientes/{cliente}/enlace-publico', [ClienteController::class, 'generarEnlacePublico']);
    
    // Membresías
    Route::apiResource('membresias', MembresiaController::class);
    Route::post('/asignar-membresia', [MembresiaController::class, 'asignarCliente']);
    Route::post('/renovar-membresia/{clienteMembresiaId}', [MembresiaController::class, 'renovarMembresia']);
    Route::post('/cambiar-membresia/{clienteMembresiaId}', [MembresiaController::class, 'cambiarMembresia']);
    Route::get('/historial-membresias/{clienteId}', [MembresiaController::class, 'historialMembresias']);
    Route::post('/cambiar-membresia/{clienteMembresiaId}', [MembresiaController::class, 'cambiarMembresia']);
    Route::get('/historial-membresias/{clienteId}', [MembresiaController::class, 'historialMembresias']);
    
    // Asistencias
    Route::apiResource('asistencias', AsistenciaController::class);
    Route::post('/registrar-ingreso', [AsistenciaController::class, 'store']);
    Route::post('/registrar-ingreso-busqueda', [AsistenciaController::class, 'registrarPorBusqueda']);
    Route::get('/estadisticas-asistencias', [AsistenciaController::class, 'estadisticas']);
    
    // Rutas solo para admin
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
    });
});
