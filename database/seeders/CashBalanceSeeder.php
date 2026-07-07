<?php

namespace Database\Seeders;

use App\Models\CashBalance;
use Illuminate\Database\Seeder;

class CashBalanceSeeder extends Seeder
{
    public function run(): void
    {
        CashBalance::create([
            'balance' => 100000000,
        ]);
    }
}
