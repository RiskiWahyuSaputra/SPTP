<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $period = now()->format('Y-m');

        $categories = Category::all();

        foreach ($categories as $category) {
            Budget::create([
                'category_id' => $category->id,
                'period' => $period,
                'allocated_amount' => 50000000,
                'used_amount' => 0,
            ]);
        }
    }
}
