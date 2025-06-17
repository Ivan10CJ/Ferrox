<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id_rol', 6)->primary();
            $table->string('nombre_rol', 50);
            $table->timestamps();
        });

        // Datos iniciales
        DB::table('roles')->insert([
            ['id_rol' => 'ADMIN', 'nombre_rol' => 'Administrador'],
            ['id_rol' => 'EMPLEA', 'nombre_rol' => 'Empleado']
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};