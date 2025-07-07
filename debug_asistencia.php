<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cliente;
use App\Models\Asistencia;
use Carbon\Carbon;

echo "=== Verificación de Cliente y Membresías ===" . PHP_EOL;

// Verificar cliente 1
$cliente = Cliente::with(['membresias' => function($query) {
    $query->where('activa', true)->orderBy('fecha_vencimiento', 'desc');
}])->find(1);

echo "CLIENTE 1:" . PHP_EOL;
if ($cliente) {
    echo "Cliente encontrado: {$cliente->nombre}" . PHP_EOL;
    echo "Estado: {$cliente->estado}" . PHP_EOL;
    echo "Membresías activas: " . $cliente->membresias->count() . PHP_EOL;
    
    if ($cliente->membresias->count() > 0) {
        $membresia = $cliente->membresias->first();
        echo "Membresía: {$membresia->nombre}" . PHP_EOL;
        echo "Fecha vencimiento: {$membresia->pivot->fecha_vencimiento}" . PHP_EOL;
        
        $hoy = Carbon::now()->toDateString();
        $asistenciaHoy = Asistencia::where('cliente_id', $cliente->id)
            ->whereDate('created_at', $hoy)
            ->first();
        
        echo "Asistencia registrada hoy: " . ($asistenciaHoy ? 'SÍ' : 'NO') . PHP_EOL;
        
        if ($asistenciaHoy) {
            echo "Hora de registro: {$asistenciaHoy->created_at}" . PHP_EOL;
        }
    } else {
        echo "❌ No tiene membresías activas" . PHP_EOL;
    }
} else {
    echo "❌ Cliente no encontrado" . PHP_EOL;
}

echo PHP_EOL . "CLIENTE 2:" . PHP_EOL;
// Verificar cliente 2
$cliente2 = Cliente::with(['membresias' => function($query) {
    $query->where('activa', true)->orderBy('fecha_vencimiento', 'desc');
}])->find(2);

if ($cliente2) {
    echo "Cliente encontrado: {$cliente2->nombre}" . PHP_EOL;
    echo "Estado: {$cliente2->estado}" . PHP_EOL;
    echo "Membresías activas: " . $cliente2->membresias->count() . PHP_EOL;
    
    if ($cliente2->membresias->count() > 0) {
        $membresia = $cliente2->membresias->first();
        echo "Membresía: {$membresia->nombre}" . PHP_EOL;
        echo "Fecha vencimiento: {$membresia->pivot->fecha_vencimiento}" . PHP_EOL;
        
        $hoy = Carbon::now()->toDateString();
        $asistenciaHoy = Asistencia::where('cliente_id', $cliente2->id)
            ->whereDate('created_at', $hoy)
            ->first();
        
        echo "Asistencia registrada hoy: " . ($asistenciaHoy ? 'SÍ' : 'NO') . PHP_EOL;
        
        if ($asistenciaHoy) {
            echo "Hora de registro: {$asistenciaHoy->created_at}" . PHP_EOL;
        }
    } else {
        echo "❌ No tiene membresías activas" . PHP_EOL;
    }
} else {
    echo "❌ Cliente no encontrado" . PHP_EOL;
}

echo PHP_EOL . "=== Verificación de Asistencias de Hoy ===" . PHP_EOL;
$asistenciasHoy = Asistencia::whereDate('created_at', Carbon::now()->toDateString())->get();
echo "Total asistencias hoy: " . $asistenciasHoy->count() . PHP_EOL;

foreach($asistenciasHoy as $asistencia) {
    $cliente = Cliente::find($asistencia->cliente_id);
    echo "- {$cliente->nombre} a las {$asistencia->created_at->format('H:i:s')}" . PHP_EOL;
}
