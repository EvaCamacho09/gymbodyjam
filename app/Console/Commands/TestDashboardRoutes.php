<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestDashboardRoutes extends Command
{
    protected $signature = 'test:dashboard-routes';
    protected $description = 'Test dashboard routes from backend';

    public function handle()
    {
        $baseUrl = 'http://localhost:8000';
        $token = '8|FVcyxbz1g3mi2jIVf18XGcAR7OCddKyZWT7RELeGc67ef4c6';
        
        $this->info('Testing dashboard routes...');
        
        try {
            // Test basic route
            $this->info('1. Testing /api/test...');
            $response = Http::get("{$baseUrl}/api/test");
            $this->info('Status: ' . $response->status());
            $this->info('Response: ' . $response->body());
            
            // Test authenticated route
            $this->info("\n2. Testing /api/dashboard/estadisticas...");
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json'
            ])->get("{$baseUrl}/api/dashboard/estadisticas");
            
            $this->info('Status: ' . $response->status());
            if ($response->successful()) {
                $data = $response->json();
                $this->info('Total clientes: ' . ($data['total_clientes'] ?? 'N/A'));
                $this->info('Clientes morosos: ' . ($data['clientes_morosos'] ?? 'N/A'));
                $this->info('Membresías count: ' . count($data['membresias'] ?? []));
            } else {
                $this->error('Error response: ' . $response->body());
            }
            
            // Test actividad route
            $this->info("\n3. Testing /api/dashboard/actividad-reciente...");
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json'
            ])->get("{$baseUrl}/api/dashboard/actividad-reciente");
            
            $this->info('Status: ' . $response->status());
            if ($response->successful()) {
                $data = $response->json();
                $this->info('Asistencias hoy: ' . ($data['asistencias_hoy'] ?? 'N/A'));
                $this->info('Últimos clientes count: ' . count($data['ultimos_clientes'] ?? []));
            } else {
                $this->error('Error response: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
        }
        
        return 0;
    }
}
