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
    Schema::create('inventarios', function (Blueprint $table) {
        $table->id();
        $table->string('codigo');
        $table->string('nombre');
        $table->string('descripcion');
        $table->enum('tipo_venta', ['kilo', 'pieza', 'rollo', 'tubo']);
        $table->integer('unidad')->nullable();
        $table->decimal('metros', 8, 2)->nullable();
        $table->decimal('precio_unidad', 8, 2);
        $table->decimal('precio_metro', 8, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
