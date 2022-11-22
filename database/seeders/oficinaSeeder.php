<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class oficinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oficinas')->insert([
            [
            'id' => 9,
            'nombre' => 'Administrador del sistema',
            ],
            [
            'id' => 1,
            'nombre' => 'Meza de partes',
            ],
            [
            'id' => 2,
            'nombre' => 'Direccion general',
            ],
            [
            'id' => 3,
            'nombre' => 'Gerencia',
            ],
            
        ]);
    }
}
