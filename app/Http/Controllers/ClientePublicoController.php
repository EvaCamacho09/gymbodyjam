<?php

namespace App\Http\Controllers;
use Google\Client;
use App\Models\Cliente;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
            ],
            'recomendaciones' => $this->getRecomendaciones($cliente->peso, $cliente->altura, $cliente->observaciones)
        ];

        return view('cliente-publico', compact('datos'));
    }

   public function getRecomendaciones($peso, $altura, $objetivo)
{
    $cacheKey = "recomendacion_{$peso}_{$altura}_" . md5($objetivo);

    // Intentar obtener desde caché
    $cachedRecommendation = Cache::get($cacheKey);
    if ($cachedRecommendation) {
        return $cachedRecommendation;
    }
$prompt = "
Eres un entrenador amable y servicial. Genera directamente una respuesta breve (máximo 2 oraciones) de motivación personalizada para un cliente de gimnasio, basada en su peso, altura y objetivo.

Importante:
- No incluyas frases como “Aquí tienes tu mensaje”, “Claro que sí”, ni encabezados.
- No uses comillas ni markdown (**negrita**, etc).
- Tu tono debe ser profesional, amigable y directo.

Altura: {$altura} cm
Peso: {$peso} kg
Objetivo: {$objetivo}
";

    $apiKey = env('GOOGLE_AI_API_KEY');
    $endpoint = env('GOOGLE_AI_ENDPOINT');

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post("{$endpoint}?key={$apiKey}", [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ]
    ]);

    if ($response->failed()) {
        return ['error' => 'Error al obtener recomendaciones'];
    }

    $data = $response->json();
    $recommendation = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No se recibió una recomendación.';

    // Guardar en caché por 24 horas
    Cache::put($cacheKey, $recommendation, now()->addHours(24));

    return $recommendation;
}
}
