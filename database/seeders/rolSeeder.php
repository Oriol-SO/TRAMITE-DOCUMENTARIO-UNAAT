<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class rolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
            'id' => 1,
            'nombre' => 'Administrador',
            'estado' => 1,
            //'oficina_id'=>9,
            ],
            [
            'id' => 2,
            'nombre' => 'Meza de partes',
            'estado' => 1,
            //'oficina_id'=>1,
            ],
            [
            'id' => 3,
            'nombre' => 'Unidad orgánica',
            'estado' => 1,
            //'oficina_id'=>2,
            ],
            
            
        ]);
    }
}
