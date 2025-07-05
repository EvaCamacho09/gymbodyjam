<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\DashboardController;

class TestDashboardAPI extends Command
{
    protected $signature = 'dashboard:test-api';
    protected $description = 'Test dashboard API endpoints';

    public function handle()
    {
        $controller = new DashboardController();
        
        $this->info('=== Testing Dashboard API ===');
        
        try {
            $this->info('Testing estadisticas endpoint...');
            $response = $controller->estadisticas();
            $data = $response->getData(true);
            $this->info('Estadisticas response:');
            $this->info(json_encode($data, JSON_PRETTY_PRINT));
            
            $this->info("\nTesting actividad-reciente endpoint...");
            $response2 = $controller->actividadReciente();
            $data2 = $response2->getData(true);
            $this->info('Actividad reciente response:');
            $this->info(json_encode($data2, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
        
        return 0;
    }
}
