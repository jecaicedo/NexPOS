<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Motor',          'color' => '#ef4444'],
            ['name' => 'Frenos',         'color' => '#f97316'],
            ['name' => 'Transmisión',    'color' => '#eab308'],
            ['name' => 'Eléctrico',      'color' => '#3b82f6'],
            ['name' => 'Carrocería',     'color' => '#8b5cf6'],
            ['name' => 'Filtros',        'color' => '#22c55e'],
            ['name' => 'Aceites',        'color' => '#14b8a6'],
            ['name' => 'Llantas',        'color' => '#6366f1'],
            ['name' => 'Cadena y Piñón', 'color' => '#ec4899'],
            ['name' => 'Accesorios',     'color' => '#64748b'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => str()->slug($cat['name']), 'color' => $cat['color'], 'is_active' => true]
            );
        }
    }
}
