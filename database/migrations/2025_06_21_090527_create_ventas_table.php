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
       Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('usuario_id');
        $table->dateTime('fecha')->useCurrent();
        $table->decimal('total', 10, 2);
        $table->unsignedBigInteger('corte_id')->nullable();
        $table->timestamps();

        $table->foreign('usuario_id')->references('id_usuario')->on('usuarios');
        $table->foreign('corte_id')->references('id')->on('cortes_caja');
       });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
