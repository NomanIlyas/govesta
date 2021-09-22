<?php

use App\Model\General\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    public function run()
    {
        // Default Languages
        Language::create([
            "code" => "en",
            "name" => "English",
        ]);
        Language::create([
            "code" => "de",
            "name" => "German",
        ]);
    }
}
