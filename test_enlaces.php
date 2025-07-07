<?php

require_once 'bootstrap/app.php';

$app = app();

echo "=== Prueba de Enlaces Públicos ===\n";

// Obtener el primer cliente
$cliente = \App\Models\Cliente::first();

if ($cliente) {
    echo "Cliente encontrado: {$cliente->nombre}\n";
    echo "Email: {$cliente->correo}\n";
    echo "Teléfono: {$cliente->telefono}\n";
    
    // Generar token si no existe
    $token = $cliente->obtenerToken();
    echo "Token: {$token}\n";
    
    // Generar URL pública
    $url = $cliente->urlPublica();
    echo "URL pública: {$url}\n";
    
    // Verificar que el cliente se puede encontrar por token
    $clienteEncontrado = \App\Models\Cliente::where('token', $token)->first();
    if ($clienteEncontrado) {
        echo "✅ Cliente encontrado correctamente por token\n";
    } else {
        echo "❌ Error: No se pudo encontrar el cliente por token\n";
    }
    
    // Mostrar información de membresía
    $membresiaActiva = $cliente->membresiaActiva();
    if ($membresiaActiva) {
        echo "Membresía activa: {$membresiaActiva->nombre}\n";
        echo "Días restantes: {$cliente->diasRestantes()}\n";
        echo "¿Es moroso?: " . ($cliente->esMoroso() ? 'Sí' : 'No') . "\n";
    } else {
        echo "Sin membresía activa\n";
    }
    
} else {
    echo "❌ No se encontró ningún cliente\n";
}

echo "\n=== Fin de la prueba ===\n";
