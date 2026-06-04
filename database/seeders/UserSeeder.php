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
            ['email' => 'admin@gpcenter.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('gpcenter'),
                'is_active'=> true,
            ]
        );
        $admin->assignRole('admin');
    }
}
