<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexpos.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $admin->assignRole('admin');

        $cajero = User::firstOrCreate(
            ['email' => 'cajero@nexpos.com'],
            [
                'name'     => 'Cajero Demo',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $cajero->assignRole('cajero');

        $mecanico = User::firstOrCreate(
            ['email' => 'mecanico@nexpos.com'],
            [
                'name'     => 'Mecánico Demo',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $mecanico->assignRole('mecanico');
    }
}
