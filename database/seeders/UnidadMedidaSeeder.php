<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadMedidaSeeder extends Seeder
{
    public function run()
    {
        DB::table('unidades_medida')->insert([
            ['nombre' => 'Metro'],
            ['nombre' => 'Pieza'],
            ['nombre' => 'Caja'],
            ['nombre' => 'Litro'],
            ['nombre' => 'Kilogramo'],
        ]);
    }
}
