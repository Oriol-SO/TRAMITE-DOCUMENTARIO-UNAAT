<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
            'id' => 1,
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'dni'=>'12345678',
            'email_verified_at' => now(),
            'rol_id'=>1,
            'password' => Hash::make('password'),
            'oficina_id'=>9,
            ],
            [
            'id' => 2,
            'name' => 'Mesa de partes',
            'email' => 'mesa@gmail.com',
            'dni'=>'98765432',
            'email_verified_at' => now(),
            'rol_id'=>2,
            'password' => Hash::make('password'),
            'oficina_id'=>1,
            ],
            [
            'id' => 3,
            'name' => 'Unidad Organica x',
            'email' => 'unidad@gmail.com',
            'dni'=>'12435676',
            'email_verified_at' => now(),
            'rol_id'=>3,
            'password' => Hash::make('password'),
            'oficina_id'=>2,
            ],

            
        ]);
    }
}
