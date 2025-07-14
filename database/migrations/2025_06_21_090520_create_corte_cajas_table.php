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
    Schema::create('corte_cajas', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->unsignedBigInteger('user_id'); // quien hizo el corte
        $table->decimal('total_general', 10, 2);
        $table->decimal('total_costo', 10, 2);
        $table->decimal('total_ganancia', 10, 2);
        $table->decimal('total_en_caja', 10, 2);
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users');
    });
}

};
