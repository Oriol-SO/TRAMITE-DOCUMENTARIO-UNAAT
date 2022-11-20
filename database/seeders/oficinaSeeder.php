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
            'id' => 1,
            'nombre' => 'Gestion Acádemica',
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
