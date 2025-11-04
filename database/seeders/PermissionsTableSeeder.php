<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        Permission::firstOrCreate([
            'key'        => 'browse_admin',
            'keyDescription'=>'vista de acceso al sistema',
            'table_name' => 'admin',
            'tableDescription'=>'Panel del Sistema'
        ]);

        $keys = [
            // 'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
            'browse_clear-cache',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');
        Permission::generateFor('permissions');
        Permission::generateFor('settings');

        Permission::generateFor('users');

        Permission::generateFor('posts');
        Permission::generateFor('categories');
        Permission::generateFor('pages');


        Permission::generateFor('category_expenses');


        $permissions = [
            'browse_cashiers'=> 'Ver cajeros',
            'add_cashiers'=> 'Agregar cajeros',
            'read_cashiers'=> 'Ver detalle de cajeros',
            
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'cashiers',
                'tableDescription'=>'Cajeros'
            ]);
        }

        

        // Administracion
        $permissions = [
            'browse_people' => 'Ver lista de personas',
            'read_people' => 'Ver detalles de una persona',
            'edit_people' => 'Editar información de personas',
            'add_people' => 'Agregar nuevas personas',
            'delete_people' => 'Eliminar personas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'people',
                'tableDescription'=>'Personas'
            ]);
        }



        // Parametros
        $permissions = [
            'browse_item_categories' => 'Ver lista de categorias de productos de ventas',
            'read_item_categories' => 'Ver detalles de categorias de productos de ventas',
            'edit_item_categories' => 'Editar información de categorias de productos de ventas',
            'add_item_categories' => 'Agregar nuevos categorias de productos de ventas',
            'delete_item_categories' => 'Eliminar categorias de productos de ventas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'item_categories',
                'tableDescription'=>'Categorias productos de ventas'
            ]);
        }

        $permissions = [
            'browse_items' => 'Ver lista de productos en ventas',
            'read_items' => 'Ver detalles de productos en ventas',
            'edit_items' => 'Editar información de productos en ventas',
            'add_items' => 'Agregar nuevos productos en ventas',
            'delete_items' => 'Eliminar productos en ventas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'items',
                'tableDescription'=>'Productos / Items en Venta'
            ]);
        }


        $permissions = [
            'browse_rooms' => 'Ver lista de salas',
            'read_rooms' => 'Ver detalles de salas',
            'edit_rooms' => 'Editar información de salas',
            'add_rooms' => 'Agregar nuevos salas',
            'delete_rooms' => 'Eliminar salas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'rooms',
                'tableDescription'=>'Salas'
            ]);
        }

     



        
        
    }
}