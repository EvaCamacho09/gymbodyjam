<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('cliente_membresia_id')->nullable()->constrained('cliente_membresia')->onDelete('set null');
            $table->datetime('fecha_ingreso');
            $table->boolean('membresia_valida')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['cliente_id', 'fecha_ingreso']);
            $table->index('fecha_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
