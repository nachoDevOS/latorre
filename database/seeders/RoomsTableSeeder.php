<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('rooms')->delete();
        
        \DB::table('rooms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Normal',
                'name' => 'Sala 1',
                'image' => NULL,
                'observation' => NULL,
                'status' => 'Disponible',
                'created_at' => '2025-11-13 10:35:25',
                'updated_at' => '2025-11-13 11:30:42',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'Normal',
                'name' => 'Sala 2',
                'image' => NULL,
                'observation' => NULL,
                'status' => 'Disponible',
                'created_at' => '2025-11-13 10:35:32',
                'updated_at' => '2025-11-13 10:35:32',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'Normal',
                'name' => 'Sala 3',
                'image' => NULL,
                'observation' => NULL,
                'status' => 'Disponible',
                'created_at' => '2025-11-13 10:35:41',
                'updated_at' => '2025-11-13 10:35:41',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
        ));
        
        
    }
}