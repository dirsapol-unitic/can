<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'dni' => '40101199',
            'name' => 'Hubert Cesar Sirlopu Quesquén',
            'email' => 'sirlces21@gmail.com',
            'password' => bcrypt('12345678'),
            'establecimiento_id'=>1,
            'nombre_establecimiento'=>'HOSPITAL NACIONAL PNP LNS',
            'servicio_id'=>1,
            'nombre_servicio'=>'Farmacia 01 - LNS',
            'rol'=>1,
            'nombres'=>'Hubert Cesar',
            'apellidos'=>'Sirlopu Quesquen',
            'grado_id'=>1,
            'grado'=>'Teniente General',
            'telefono'=>'991589528',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
    }
}
