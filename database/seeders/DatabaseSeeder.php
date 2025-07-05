<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador
        \App\Models\User::create([
            'name' => 'Administrador',
            'email' => 'admin@gym.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Crear usuario secretario
        \App\Models\User::create([
            'name' => 'Secretario',
            'email' => 'secretario@gym.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'secretario',
        ]);

        // Crear algunas membresías de ejemplo
        \App\Models\Membresia::create([
            'nombre' => 'Mensual',
            'duracion_dias' => 30,
            'precio' => 50.00,
            'descripcion' => 'Membresía mensual con acceso completo al gimnasio',
            'activa' => true,
        ]);

        \App\Models\Membresia::create([
            'nombre' => 'Trimestral',
            'duracion_dias' => 90,
            'precio' => 135.00,
            'descripcion' => 'Membresía trimestral con 10% de descuento',
            'activa' => true,
        ]);

        \App\Models\Membresia::create([
            'nombre' => 'Anual',
            'duracion_dias' => 365,
            'precio' => 480.00,
            'descripcion' => 'Membresía anual con 20% de descuento',
            'activa' => true,
        ]);

        // Crear algunos clientes de ejemplo
        \App\Models\Cliente::create([
            'nombre' => 'Juan Pérez',
            'cedula' => '12345678',
            'correo' => 'juan.perez@email.com',
            'telefono' => '123-456-7890',
            'estado' => 'activo',
        ]);

        \App\Models\Cliente::create([
            'nombre' => 'María García',
            'cedula' => '87654321',
            'correo' => 'maria.garcia@email.com',
            'telefono' => '098-765-4321',
            'estado' => 'activo',
        ]);

        // Crear más clientes de ejemplo
        $clientesAdicionales = [
            ['nombre' => 'Carlos Rodríguez', 'cedula' => '11223344', 'correo' => 'carlos.rodriguez@email.com', 'telefono' => '111-222-3333'],
            ['nombre' => 'Ana Martínez', 'cedula' => '55667788', 'correo' => 'ana.martinez@email.com', 'telefono' => '555-666-7777'],
            ['nombre' => 'Pedro Sánchez', 'cedula' => '99887766', 'correo' => 'pedro.sanchez@email.com', 'telefono' => '999-888-7777'],
        ];

        foreach ($clientesAdicionales as $clienteData) {
            \App\Models\Cliente::create(array_merge($clienteData, ['estado' => 'activo']));
        }

        // Asignar membresías a clientes
        $clientes = \App\Models\Cliente::all();
        $membresias = \App\Models\Membresia::all();

        foreach ($clientes as $index => $cliente) {
            $membresia = $membresias[$index % $membresias->count()];
            $fechaInicio = now()->subDays(rand(5, 30));
            
            \App\Models\ClienteMembresia::create([
                'cliente_id' => $cliente->id,
                'membresia_id' => $membresia->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_vencimiento' => $fechaInicio->copy()->addDays($membresia->duracion_dias),
                'precio_pagado' => $membresia->precio,
                'estado_pago' => 'pagado',
            ]);
        }

        // Crear asistencias de ejemplo
        $this->crearAsistenciasEjemplo();
    }

    private function crearAsistenciasEjemplo()
    {
        $clientes = \App\Models\Cliente::all();
        
        // Crear asistencias para los últimos 30 días
        for ($i = 0; $i < 30; $i++) {
            $fecha = now()->subDays($i);
            
            // Seleccionar aleatoriamente algunos clientes para ese día
            $clientesDelDia = $clientes->random(rand(2, 5));
            
            foreach ($clientesDelDia as $cliente) {
                $clienteMembresia = $cliente->membresiaActiva();
                
                \App\Models\Asistencia::create([
                    'cliente_id' => $cliente->id,
                    'cliente_membresia_id' => $clienteMembresia?->pivot->id,
                    'fecha_ingreso' => $fecha->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59)),
                    'membresia_valida' => $clienteMembresia ? !$cliente->esMoroso() : false,
                    'observaciones' => $i < 5 ? 'Asistencia reciente' : null,
                ]);
            }
        }
    }
}
