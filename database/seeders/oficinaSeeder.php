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
            'nombre' => 'Administracion',
            ],
            [
            'id' => 1,
            'nombre' => 'Mesa de partes',
            ],
            [
            'id' => 2,
            'nombre' => 'Oficina de Tecnologías de la Información',
            ],
            [
            'id' => 3,
            'nombre' => 'Dirección General de Administración',
            ],
            [
            'id' => 4,
            'nombre' => 'Secretaría General',
            ],
            [
            'id' => 5,
            'nombre' => 'Oficina de Planeamiento y Presupuesto',
            ],
            [
            'id' => 6,
            'nombre' => 'Oficina de Cooperación y Relaciones Internacionales',
            ],
            [
            'id' =>7,
            'nombre' => 'Oficina de Comunicación e Imagen Institucional',
            ],
            
        ]);
    }
}
