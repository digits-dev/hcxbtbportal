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
                'name'              => 'Item Master',
                'type'              => 'Route',
                'path'              => 'ItemMasters\ItemMastersControllerGetIndex',
                'slug'              => 'item_masters',
                'icon'              => 'fa-solid fa-box-archive',
                'parent_id'         => 7,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 3
            ],
            [
                'name'              => 'Item Inventory',
                'type'              => 'Route',
                'path'              => 'ItemInventories\ItemInventoriesControllerGetIndex',
                'slug'              => 'item_inventories',
                'icon'              => 'fa-solid fa-check',
                'parent_id'         => 7,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 1
            ],
            [
                'name'              => 'Deliveries',
                'type'              => 'Route',
                'path'              => 'Deliveries\DeliveriesControllerGetIndex',
                'slug'              => 'deliveries',
                'icon'              => 'fa-solid fa-truck',
                'parent_id'         => 7,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 2
            ],
            [
                'name'              => 'Statuses',
                'type'              => 'Route',
                'path'              => 'Statuses\StatusesControllerGetIndex',
                'slug'              => 'statuses',
                'icon'              => 'fa-regular fa-circle',
                'parent_id'         => 7,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 4
            ],
            [
                'name'              => 'Submaster',
                'type'              => 'URL',
                'path'              => '#',
                'icon'              => 'fa-solid fa-bars',
                'parent_id'         => 0,
                'is_active'         => 1,
                'is_dashboard'      => 1,
                'id_adm_privileges' => 1,
                'sorting'           => 3
            ],
            [
                'name'              => 'Order History',
                'type'              => 'Route',
                'path'              => 'OrderHistories\OrderHistoriesControllerGetIndex',
                'slug'              => 'order_histories',
                'icon'              => 'fa-solid fa-clock-rotate-left',
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