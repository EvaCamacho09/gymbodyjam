<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeasurementsToClientesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->float('peso')->nullable()->comment('Peso en kilogramos (kg)');
            $table->float('altura')->nullable()->comment('Altura en centímetros (cm)');
            $table->float('imc')->nullable()->comment('Índice de masa corporal (peso/altura²)');
            $table->float('porcentaje_grasa')->nullable()->comment('Porcentaje de grasa corporal');
            $table->float('masa_muscular')->nullable()->comment('Masa muscular estimada');
            $table->float('cintura')->nullable()->comment('Medida de la cintura en cm');
            $table->float('cadera')->nullable()->comment('Medida de la cadera en cm');
            $table->float('pecho_torax')->nullable()->comment('Medida de pecho/tórax en cm');
            $table->float('biceps_relajado')->nullable()->comment('Bíceps relajado en cm');
            $table->float('biceps_contraido')->nullable()->comment('Bíceps contraído en cm');
            $table->float('antebrazo')->nullable()->comment('Antebrazo en cm');
            $table->float('muslo')->nullable()->comment('Muslo en cm');
            $table->float('pantorrilla')->nullable()->comment('Pantorrilla en cm');
            $table->integer('frecuencia_cardiaca')->nullable()->comment('Frecuencia cardíaca en reposo (lpm)');
            $table->string('presion_arterial')->nullable()->comment('Presión arterial (ej: 120/80)');
            $table->text('observaciones')->nullable()->comment('Notas u observaciones generales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'peso',
                'altura',
                'imc',
                'porcentaje_grasa',
                'masa_muscular',
                'cintura',
                'cadera',
                'pecho_torax',
                'biceps_relajado',
                'biceps_contraido',
                'antebrazo',
                'muslo',
                'pantorrilla',
                'frecuencia_cardiaca',
                'presion_arterial',
                'observaciones',
            ]);
        });
    }
}
