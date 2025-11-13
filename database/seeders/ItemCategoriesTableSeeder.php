<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_categories')->delete();
        
        \DB::table('item_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Gaseosa',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2025-11-13 10:36:49',
                'updated_at' => '2025-11-13 10:36:49',
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