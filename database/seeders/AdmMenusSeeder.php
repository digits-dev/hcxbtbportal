<?php

namespace Database\Seeders;

use App\Models\AdmModels\AdmMenus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $menus = [
            [
                'name'              => 'Dashboard',
                'type'              => 'Route',
                'path'              => 'Dashboard\DashboardControllerGetIndex',
                'slug'              => 'dashboard',
                'icon'              => 'fa-solid fa-chart-simple',
                'parent_id'         => 0,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 1
            ],
            [
                'name'              => 'Orders',
                'type'              => 'Route',
                'path'              => 'Orders\OrdersControllerGetIndex',
                'slug'              => 'orders',
                'icon'              => 'fa-solid fa-list',
                'parent_id'         => 0,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 2
            ],
            [
                'name'              => 'Item Inventory',
                'type'              => 'Route',
                'path'              => 'ItemInventories\ItemInventoriesControllerGetIndex',
                'slug'              => 'item_inventories',
                'icon'              => 'fa-solid fa-check',
                'parent_id'         => 0,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 3
            ],
            [
                'name'              => 'Item Master',
                'type'              => 'Route',
                'path'              => 'ItemMasters\ItemMastersControllerGetIndex',
                'slug'              => 'item_masters',
                'icon'              => 'fa-solid fa-box-archive',
                'parent_id'         => 0,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 4
            ],
        ];

        foreach ($menus as $menu) {
            AdmMenus::updateOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }


    }

   

}