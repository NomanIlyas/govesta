<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    public function run()
    {
        $countries = json_decode(file_get_contents(storage_path("app/json/countries.json")))->countries;
        $states = json_decode(file_get_contents(storage_path("app/json/states.json")))->states;
        $cities = json_decode(file_get_contents(storage_path("app/json/cities.json")))->cities;

        $countryDb = [];
        $stateDb = [];
        $cityDb1 = [];
        $cityDb2 = [];
        $cityDb3 = [];
        $cityDb4 = [];
        $cityDb5 = [];
        $disrictDb = [];

        foreach ($countries as $countryItem) {

            // Country
            $country = array(
                'id' => $countryItem->id,
                'code' => $countryItem->sortname,
                'phone_prefix' => intval($countryItem->phoneCode),
            );
            $countryDb[] = $country;

        }

        foreach ($states as $stateItem) {

            // State
            $state = array(
                'id' => $stateItem->id,
                'country_id' => intval($stateItem->country_id),
            );
            $stateDb[$stateItem->id] = $state;
        }

        //City
        foreach ($cities as $cityItem) {
            $stateId = (int) $cityItem->state_id;
            if (isset($stateDb[$stateId])) {
                $cityId = (int) $cityItem->id;
                $city = array(
                    'id' => $cityId,
                    'state_id' => $stateId,
                    'country_id' => (int) $stateDb[$stateId]['country_id'],
                );
                if ($cityId <= 10000) {
                    $cityDb1[] = $city;
                } else if ($cityId > 10000 && $cityId <= 20000) {
                    $cityDb2[] = $city;
                } else if ($cityId > 20000 && $cityId <= 30000) {
                    $cityDb3[] = $city;
                } else if ($cityId > 30000 && $cityId <= 40000) {
                    $cityDb4[] = $city;
                } else {
                    $cityDb5[] = $city;
                }
            }
        }

        $disricts = ["Charlottenburg-Wilmersdorf", "Friedrichshain-Kreuzberg", "Lichtenberg", "Marzahn-Hellersdorf", "Mitte", "Neukölln", "Pankow", "Reinickendorf", "Spandau", "Steglitz-Zehlendorf", "Tempelhof-Schöneberg", "Treptow-Köpenick"];

        foreach ($disricts as $disrictItem) {
            $disrict = array(
                'name' => $disrictItem,
                'slug' => str_slug($disrictItem),
                'city_id' => 19014
            );
            $disrictDb[] = $disrict;
        }

        DB::table('countries')->insert($countryDb);
        DB::table('states')->insert($stateDb);
        DB::table('cities')->insert($cityDb1);
        DB::table('cities')->insert($cityDb2);
        DB::table('cities')->insert($cityDb3);
        DB::table('cities')->insert($cityDb4);
        DB::table('cities')->insert($cityDb5);
        DB::table('districts')->insert($disrictDb);
    }
}
