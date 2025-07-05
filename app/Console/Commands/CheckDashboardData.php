<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ClienteMembresia;

class CheckDashboardData extends Command
{
    protected $signature = 'dashboard:check';
    protected $description = 'Check dashboard data';

    public function handle()
    {
        $this->info('=== Dashboard Data Check ===');
        
        // Clientes
        $totalClientes = Cliente::count();
        $clientesActivos = Cliente::where('estado', 'activo')->count();
        $this->info("Total clientes: {$totalClientes}");
        $this->info("Clientes activos: {$clientesActivos}");
        
        // Membresías
        $totalMembresias = Membresia::count();
        $membresiasActivas = Membresia::where('activa', true)->count();
        $this->info("Total membresías: {$totalMembresias}");
        $this->info("Membresías activas: {$membresiasActivas}");
        
        // Relaciones
        $clientesConMembresia = ClienteMembresia::count();
        $this->info("Relaciones cliente-membresía: {$clientesConMembresia}");
        
        // Membresías con clientes
        $this->info("\n=== Membresías ===");
        Membresia::with('clientes')->get()->each(function($membresia) {
            $count = $membresia->clientes->count();
            $this->info("{$membresia->nombre}: {$count} clientes");
        });
        
        return 0;
    }
}
