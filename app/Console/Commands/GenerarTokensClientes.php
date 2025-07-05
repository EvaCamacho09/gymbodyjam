<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;

class GenerarTokensClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:generar-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar tokens únicos para todos los clientes que no tengan uno';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generando tokens para clientes...');
        
        $clientesSinToken = Cliente::whereNull('token')->get();
        
        if ($clientesSinToken->isEmpty()) {
            $this->info('Todos los clientes ya tienen token asignado.');
            return;
        }
        
        $this->info("Encontrados {$clientesSinToken->count()} clientes sin token.");
        
        $bar = $this->output->createProgressBar($clientesSinToken->count());
        $bar->start();
        
        foreach ($clientesSinToken as $cliente) {
            $cliente->generarToken();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('¡Tokens generados exitosamente!');
        
        // Mostrar algunos ejemplos de URLs
        $this->newLine();
        $this->info('Ejemplos de URLs generadas:');
        $ejemplos = Cliente::whereNotNull('token')->take(3)->get();
        
        foreach ($ejemplos as $cliente) {
            $this->line("• {$cliente->nombre}: {$cliente->urlPublica()}");
        }
    }
}
