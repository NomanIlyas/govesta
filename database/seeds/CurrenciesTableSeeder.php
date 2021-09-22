<?php

use App\Model\General\Currency;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    public function run()
    {
        // Default Currencies
        Currency::create([
            "code" => "usd",
            "symbol" => "$",
            "name" => "Dollar",
        ]);
        Currency::create([
            "code" => "eur",
            "symbol" => "€",
            "name" => "Euro",
        ]);
        Currency::create([
            "code" => "gbp",
            "symbol" => "£",
            "name" => "Pound",
        ]);
    }
}
