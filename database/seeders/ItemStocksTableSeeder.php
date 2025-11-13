<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemStocksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_stocks')->delete();
        
        \DB::table('item_stocks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'item_id' => 1,
                'lote' => '22343243244',
                'quantity' => '50.00',
                'stock' => '50.00',
                'pricePurchase' => '10.00',
                'priceSale' => '13.00',
                'type' => 'Ingreso',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2025-11-13 10:38:44',
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
                'item_id' => 1,
                'lote' => '5432154325',
                'quantity' => '5.00',
                'stock' => '5.00',
                'pricePurchase' => '10.00',
                'priceSale' => '13.00',
                'type' => 'Ingreso',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2025-11-13 10:39:51',
                'updated_at' => '2025-11-13 10:39:51',
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
                'item_id' => 1,
                'lote' => '4321432',
                'quantity' => '1.00',
                'stock' => '1.00',
                'pricePurchase' => '10.00',
                'priceSale' => '13.00',
                'type' => 'Ingreso',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2025-11-13 10:41:00',
                'updated_at' => '2025-11-13 10:41:00',
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