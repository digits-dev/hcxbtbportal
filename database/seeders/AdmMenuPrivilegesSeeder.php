<?php

namespace Database\Seeders;

use App\Models\AdmModels\admMenusPrivileges;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmMenuPrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'id_adm_menus' => 1,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 2,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 3,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 4,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 5,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 6,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 7,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 8,
                'id_adm_privileges' => 1
            ],
            [
                'id_adm_menus' => 9,
                'id_adm_privileges' => 1
            ],
        ];

        foreach ($menus as $menu) {
            admMenusPrivileges::updateOrCreate(
                [
                    'id_adm_menus' => $menu['id_adm_menus'],
                    'id_adm_privileges' => $menu['id_adm_privileges'],
                ]
            );
        }
    }
}