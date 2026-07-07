<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'PO Produk', 'code' => 'PO', 'is_po_produk' => true],
            ['name' => 'Operasional', 'code' => 'OPS', 'is_po_produk' => false],
            ['name' => 'ATK', 'code' => 'ATK', 'is_po_produk' => false],
            ['name' => 'Perjalanan Dinas', 'code' => 'SPD', 'is_po_produk' => false],
            ['name' => 'Marketing', 'code' => 'MKT', 'is_po_produk' => false],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
