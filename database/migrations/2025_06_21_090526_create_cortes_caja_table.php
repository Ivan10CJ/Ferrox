<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
       Schema::create('cortes_caja', function (Blueprint $table) {
        $table->id();
        $table->date('fecha_inicio');
        $table->date('fecha_fin')->nullable();
        $table->decimal('total', 10, 2);
        $table->dateTime('creado_en')->useCurrent();
       });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cortes_caja');
    }
};
