<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GPCenterSeeder extends Seeder
{
    /**
     * Cambia a false para deshabilitar este seeder sin borrarlo.
     */
    protected bool $enabled = true;

    public function run(): void
    {
        if (! $this->enabled) {
            $this->command->warn('GPCenterSeeder deshabilitado — saltando.');
            return;
        }

        // ── 1. Roles y permisos ───────────────────────────────────────
        $this->call(RolesAndPermissionsSeeder::class);

        // ── 2. Configuración del negocio ──────────────────────────────
        $settings = [
            'app_name'        => 'GPCenter',
            'business_name'   => 'GP Center',
            'business_address'=> '',           // TODO: dirección
            'business_phone'  => '',           // TODO: teléfono
            'business_email'  => '',           // TODO: correo
            'business_nit'    => '',           // TODO: NIT
            'business_city'   => '',           // TODO: ciudad
            'currency_symbol' => '$',
            'currency_code'   => 'COP',
            'tax_enabled'     => '0',
            'tax_percent'     => '19',
            'tax_name'        => 'IVA',
            'invoice_footer'  => '¡Gracias por su compra!',
            'low_stock_alert' => '1',
            'theme'           => 'dark',
            'thermal_width'   => '80',
        ];

        foreach ($settings as $key => $value) {
            BusinessSetting::set($key, $value);
        }

        // ── 3. Usuario administrador ──────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@gpcenter.com'],
            [
                'name'      => 'Administrador',
                'password'  => Hash::make('gpcenter'),
                'is_active' => true,
            ]
        );
        $admin->syncRoles('admin');

        // ── 4. Cliente genérico (Consumidor Final) ────────────────────
        Customer::firstOrCreate(
            ['is_generic' => true],
            [
                'name'            => 'Consumidor Final',
                'document_type'   => 'CC',
                'document_number' => '0000000000',
                'is_active'       => true,
            ]
        );

        $this->command->info('GPCenterSeeder ejecutado correctamente.');
    }
}
