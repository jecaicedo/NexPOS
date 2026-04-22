<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente genérico obligatorio
        Customer::firstOrCreate(
            ['is_generic' => true],
            [
                'name'            => 'Consumidor Final',
                'document_type'   => 'CC',
                'document_number' => '0000000000',
                'is_active'       => true,
            ]
        );

        $customers = [
            ['name' => 'Carlos Rodríguez',  'document_number' => '1020304050', 'phone' => '311 222 3344', 'city' => 'Bogotá'],
            ['name' => 'María García',       'document_number' => '1030405060', 'phone' => '315 444 5566', 'city' => 'Medellín'],
            ['name' => 'Luis Herrera',       'document_number' => '1040506070', 'phone' => '300 777 8899', 'city' => 'Cali'],
            ['name' => 'Ana Torres',         'document_number' => '1050607080', 'phone' => '316 111 2233', 'city' => 'Barranquilla'],
            ['name' => 'Pedro Martínez',     'document_number' => '1060708090', 'phone' => '312 999 0011', 'city' => 'Bogotá'],
            ['name' => 'Taller El Rápido',   'document_number' => '900123456-1', 'phone' => '601 234 5678', 'city' => 'Bogotá', 'document_type' => 'NIT'],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(
                ['document_number' => $data['document_number']],
                array_merge($data, ['document_type' => $data['document_type'] ?? 'CC', 'is_active' => true])
            );
        }
    }
}
