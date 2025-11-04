<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DataTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('data_types')->delete();
        
        \DB::table('data_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'users',
                'slug' => 'users',
                'display_name_singular' => 'User',
                'display_name_plural' => 'Users',
                'icon' => 'voyager-person',
                'model_name' => 'TCG\\Voyager\\Models\\User',
                'policy_name' => 'TCG\\Voyager\\Policies\\UserPolicy',
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"desc","default_search_key":null,"scope":null}',
                'created_at' => '2024-10-18 14:28:26',
                'updated_at' => '2025-04-07 16:18:35',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'menus',
                'slug' => 'menus',
                'display_name_singular' => 'Menu',
                'display_name_plural' => 'Menus',
                'icon' => 'voyager-list',
                'model_name' => 'TCG\\Voyager\\Models\\Menu',
                'policy_name' => NULL,
                'controller' => '',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2024-10-18 14:28:26',
                'updated_at' => '2024-10-18 14:28:26',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'roles',
                'slug' => 'roles',
                'display_name_singular' => 'Role',
                'display_name_plural' => 'Roles',
                'icon' => 'voyager-lock',
                'model_name' => 'TCG\\Voyager\\Models\\Role',
                'policy_name' => NULL,
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2024-10-18 14:28:26',
                'updated_at' => '2024-10-18 14:28:26',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'posts',
                'slug' => 'posts',
                'display_name_singular' => 'Post',
                'display_name_plural' => 'Posts',
                'icon' => 'voyager-news',
                'model_name' => 'TCG\\Voyager\\Models\\Post',
                'policy_name' => 'TCG\\Voyager\\Policies\\PostPolicy',
                'controller' => '',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2024-10-18 14:28:45',
                'updated_at' => '2024-10-18 14:28:45',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'pages',
                'slug' => 'pages',
                'display_name_singular' => 'Page',
                'display_name_plural' => 'Pages',
                'icon' => 'voyager-file-text',
                'model_name' => 'TCG\\Voyager\\Models\\Page',
                'policy_name' => NULL,
                'controller' => '',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2024-10-18 14:28:45',
                'updated_at' => '2024-10-18 14:28:45',
            ),
            5 => 
            array (
                'id' => 8,
                'name' => 'people',
                'slug' => 'people',
                'display_name_singular' => 'Persona',
                'display_name_plural' => 'Personas',
                'icon' => 'fa-solid fa-person',
                'model_name' => 'App\\Models\\Person',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2025-04-07 09:43:00',
                'updated_at' => '2025-11-04 11:15:23',
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'category_expenses',
                'slug' => 'category-expenses',
                'display_name_singular' => 'Categoría de Gasto',
                'display_name_plural' => 'Categorías de Gastos',
                'icon' => 'fa-solid fa-layer-group',
                'model_name' => 'App\\Models\\CategoryExpense',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2025-11-03 23:39:02',
                'updated_at' => '2025-11-03 23:45:23',
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'item_categories',
                'slug' => 'item-categories',
                'display_name_singular' => 'Categoría de Producto',
                'display_name_plural' => 'Categorías de Productos',
                'icon' => 'fa-solid fa-layer-group',
                'model_name' => 'App\\Models\\ItemCategory',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2025-11-04 00:05:02',
                'updated_at' => '2025-11-04 00:05:02',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'items',
                'slug' => 'items',
                'display_name_singular' => 'Productos en Venta',
                'display_name_plural' => 'Productos en Ventas',
                'icon' => 'voyager-bag',
                'model_name' => 'App\\Models\\Item',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2025-11-04 00:10:57',
                'updated_at' => '2025-11-04 00:14:53',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'rooms',
                'slug' => 'rooms',
                'display_name_singular' => 'Sala',
                'display_name_plural' => 'Salas',
                'icon' => 'fa-solid fa-gamepad',
                'model_name' => 'App\\Models\\Room',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2025-11-04 11:15:12',
                'updated_at' => '2025-11-04 12:25:24',
            ),
        ));
        
        
    }
}