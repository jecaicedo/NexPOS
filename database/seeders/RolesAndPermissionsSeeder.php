<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',
            // Productos
            'view products', 'create products', 'edit products', 'delete products', 'manage stock',
            // Categorías
            'view categories', 'create categories', 'edit categories', 'delete categories',
            // Clientes
            'view customers', 'create customers', 'edit customers', 'delete customers',
            // Ventas
            'view sales', 'create sales', 'cancel sales',
            // Taller
            'view workshop', 'create workshop', 'edit workshop', 'delete workshop',
            // Empleados
            'view employees', 'create employees', 'edit employees', 'delete employees',
            // Reportes
            'view reports', 'export reports',
            // Configuración
            'view settings', 'edit settings',
            // Usuarios
            'view users', 'create users', 'edit users', 'delete users',
            // Logs
            'view logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rol Admin — acceso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Rol Cajero — solo ventas y consultas
        $cajero = Role::firstOrCreate(['name' => 'cajero']);
        $cajero->syncPermissions([
            'view dashboard',
            'view products',
            'view categories',
            'view customers', 'create customers', 'edit customers',
            'view sales', 'create sales',
            'view workshop', 'create workshop', 'edit workshop',
        ]);

        // Rol Mecánico — solo taller
        $mecanico = Role::firstOrCreate(['name' => 'mecanico']);
        $mecanico->syncPermissions([
            'view dashboard',
            'view products',
            'view workshop', 'create workshop', 'edit workshop',
        ]);
    }
}
