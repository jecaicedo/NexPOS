<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(
            ['is_generic' => true],
            [
                'name'            => 'Consumidor Final',
                'document_type'   => 'CC',
                'document_number' => '0000000000',
                'is_active'       => true,
            ]
        );
    }
}
