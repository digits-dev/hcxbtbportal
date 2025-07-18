<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmPrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Super Administrator',
                'is_superadmin' => 1,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Home Credit Staff',
                'is_superadmin' => 0,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Accounting',
                'is_superadmin' => 0,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Warehouse',
                'is_superadmin' => 0,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'WIMS',
                'is_superadmin' => 0,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'ECOMM',
                'is_superadmin' => 0,
                'theme_color'   => 'skin-blue',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $priv) {
            DB::table('adm_privileges')->updateOrInsert(['name' => $priv['name']], $priv);
        }

    }
}