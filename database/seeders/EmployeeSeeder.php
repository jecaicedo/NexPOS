<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['name' => 'Juan Mecánico',    'document_number' => '10001001', 'phone' => '310 100 1001', 'specialty' => 'Motor',        'labor_rate' => 35000],
            ['name' => 'Diego Frenos',     'document_number' => '10002002', 'phone' => '310 200 2002', 'specialty' => 'Frenos',       'labor_rate' => 30000],
            ['name' => 'Andrés Eléctrico', 'document_number' => '10003003', 'phone' => '310 300 3003', 'specialty' => 'Eléctrico',    'labor_rate' => 40000],
            ['name' => 'Luis Multimarca',  'document_number' => '10004004', 'phone' => '310 400 4004', 'specialty' => 'General',      'labor_rate' => 28000],
        ];

        foreach ($employees as $data) {
            Employee::firstOrCreate(
                ['document_number' => $data['document_number']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
