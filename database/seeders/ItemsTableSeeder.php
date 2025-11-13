<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('items')->delete();
        
        \DB::table('items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'itemCategory_id' => 1,
                'image' => 'items/November2025/q6avrIxLxH1NNok03Fyfday13am.avif',
                'name' => 'Cocacola 2 Litro',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2025-11-13 10:37:53',
                'updated_at' => '2025-11-13 10:37:53',
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