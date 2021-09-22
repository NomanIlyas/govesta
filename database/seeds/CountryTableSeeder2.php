<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder2 extends Seeder
{
    public function run()
    {
        $countries = json_decode(file_get_contents(storage_path("app/json/countries.json")))->countries;

        $countryDb = [];

        foreach ($countries as $countryItem) {

            // Country
            $country = array(
                'country_id' => $countryItem->id,
                'locale' => 'en',
                'name' => $countryItem->name,
                'slug' => str_slug($countryItem->name)
            );
            $countryDb[] = $country;

        }

        DB::table('countries_translations')->insert($countryDb);
    }
}
