<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GPCenterSeeder::class,
            RolesAndPermissionsSeeder::class,
            BusinessSettingSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
            // EmployeeSeeder::class,
        ]);
    }
}
