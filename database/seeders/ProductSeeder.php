<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Motor
            ['category' => 'Motor', 'name' => 'Bujía NGK CR7HSA', 'sku' => 'MOT-001', 'purchase_price' => 8000,  'sale_price' => 15000, 'stock' => 50, 'min_stock' => 10],
            ['category' => 'Motor', 'name' => 'Pistón Ø54mm AKT 125', 'sku' => 'MOT-002', 'purchase_price' => 35000, 'sale_price' => 65000, 'stock' => 12, 'min_stock' => 3],
            ['category' => 'Motor', 'name' => 'Empaque Motor completo', 'sku' => 'MOT-003', 'purchase_price' => 25000, 'sale_price' => 45000, 'stock' => 8,  'min_stock' => 3],

            // Frenos
            ['category' => 'Frenos', 'name' => 'Pastilla de freno delantera', 'sku' => 'FRN-001', 'purchase_price' => 12000, 'sale_price' => 22000, 'stock' => 30, 'min_stock' => 5],
            ['category' => 'Frenos', 'name' => 'Zapata de freno trasera',     'sku' => 'FRN-002', 'purchase_price' => 8000,  'sale_price' => 16000, 'stock' => 25, 'min_stock' => 5],
            ['category' => 'Frenos', 'name' => 'Disco de freno 220mm',        'sku' => 'FRN-003', 'purchase_price' => 45000, 'sale_price' => 80000, 'stock' => 6,  'min_stock' => 2],

            // Filtros
            ['category' => 'Filtros', 'name' => 'Filtro de aire AKT 125',  'sku' => 'FLT-001', 'purchase_price' => 5000,  'sale_price' => 10000, 'stock' => 40, 'min_stock' => 10],
            ['category' => 'Filtros', 'name' => 'Filtro de aceite genérico','sku' => 'FLT-002', 'purchase_price' => 6000,  'sale_price' => 12000, 'stock' => 35, 'min_stock' => 10],

            // Aceites
            ['category' => 'Aceites', 'name' => 'Aceite 4T 10W40 1L',  'sku' => 'ACE-001', 'purchase_price' => 18000, 'sale_price' => 32000, 'stock' => 60, 'min_stock' => 10],
            ['category' => 'Aceites', 'name' => 'Aceite 2T Sintético 1L','sku' => 'ACE-002', 'purchase_price' => 22000, 'sale_price' => 38000, 'stock' => 30, 'min_stock' => 5],

            // Cadena
            ['category' => 'Cadena y Piñón', 'name' => 'Kit Cadena + Piñón 428H',  'sku' => 'CAD-001', 'purchase_price' => 55000, 'sale_price' => 95000, 'stock' => 10, 'min_stock' => 2],
            ['category' => 'Cadena y Piñón', 'name' => 'Piñón corona 428 42T',      'sku' => 'CAD-002', 'purchase_price' => 18000, 'sale_price' => 35000, 'stock' => 15, 'min_stock' => 3],

            // Eléctrico
            ['category' => 'Eléctrico', 'name' => 'Batería 12V 5Ah',     'sku' => 'ELE-001', 'purchase_price' => 55000, 'sale_price' => 95000, 'stock' => 8,  'min_stock' => 2],
            ['category' => 'Eléctrico', 'name' => 'Regulador de voltaje', 'sku' => 'ELE-002', 'purchase_price' => 35000, 'sale_price' => 65000, 'stock' => 5,  'min_stock' => 2],
            ['category' => 'Eléctrico', 'name' => 'Bobina de encendido',  'sku' => 'ELE-003', 'purchase_price' => 40000, 'sale_price' => 75000, 'stock' => 3,  'min_stock' => 2],

            // Llantas
            ['category' => 'Llantas', 'name' => 'Llanta 80/100-14 delantera', 'sku' => 'LLT-001', 'purchase_price' => 65000,  'sale_price' => 110000, 'stock' => 4,  'min_stock' => 2],
            ['category' => 'Llantas', 'name' => 'Llanta 90/90-18 trasera',    'sku' => 'LLT-002', 'purchase_price' => 75000,  'sale_price' => 130000, 'stock' => 4,  'min_stock' => 2],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();
            Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id'    => $category?->id,
                    'name'           => $data['name'],
                    'purchase_price' => $data['purchase_price'],
                    'sale_price'     => $data['sale_price'],
                    'stock'          => $data['stock'],
                    'min_stock'      => $data['min_stock'],
                    'unit'           => 'unidad',
                    'is_active'      => true,
                    'track_stock'    => true,
                ]
            );
        }
    }
}
