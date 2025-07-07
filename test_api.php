<?php

// Script para probar la API de asistencia
echo "=== Prueba de API de Asistencia ===" . PHP_EOL;

$url = 'http://127.0.0.1:8000/api/asistencia/registrar';
$data = json_encode(['cliente_id' => 2]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $httpCode" . PHP_EOL;
echo "Error: $error" . PHP_EOL;
echo "Response:" . PHP_EOL;
echo $response . PHP_EOL;
