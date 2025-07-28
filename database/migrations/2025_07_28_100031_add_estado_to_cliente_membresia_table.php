<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cliente_membresia', function (Blueprint $table) {
            $table->boolean('estado')->default(false)->after('estado_pago');
        });
    }

    public function down()
    {
        Schema::table('cliente_membresia', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
