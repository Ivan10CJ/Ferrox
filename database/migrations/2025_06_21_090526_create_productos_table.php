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
      Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo', 50)->unique();
        $table->string('nombre', 100);
        $table->unsignedBigInteger('unidad_base_id');
        $table->decimal('precio', 10, 2);
        $table->decimal('stock', 10, 2);
        $table->timestamps();

        $table->foreign('unidad_base_id')->references('id')->on('unidades_medida');
      });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
