<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Currency::upsert([
            ['code' => 'USD', 'symbol' => '$', 'exchange_rate' => 1],
            ['code' => 'SAR', 'symbol' => 'ر.س', 'exchange_rate' => 3.75],

        ], ['code'], ['symbol', 'exchange_rate']);
    }
}
