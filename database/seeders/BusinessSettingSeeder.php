<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'business_name'     => 'Taller & Repuestos NexPOS',
            'business_address'  => 'Calle 10 #25-30, Centro',
            'business_phone'    => '300 000 0000',
            'business_email'    => 'info@nexpos.com',
            'business_nit'      => '900.000.000-1',
            'business_city'     => 'Bogotá',
            'currency_symbol'   => '$',
            'currency_code'     => 'COP',
            'tax_enabled'       => '0',
            'tax_percent'       => '19',
            'tax_name'          => 'IVA',
            'invoice_footer'    => '¡Gracias por su compra!',
            'low_stock_alert'   => '1',
            'theme'             => 'dark',
            'thermal_width'     => '80', // mm
        ];

        foreach ($settings as $key => $value) {
            BusinessSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
