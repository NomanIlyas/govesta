<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    public function run()
    {
        $cities = json_decode(file_get_contents(storage_path("app/json/cities.json")))->cities;
        $states = json_decode(file_get_contents(storage_path("app/json/states.json")))->states;

        $stateDb = [];
        $cityDb1 = [];
        $cityDb2 = [];
        $cityDb3 = [];
        $cityDb4 = [];
        $cityDb5 = [];

        foreach ($states as $stateItem) {

            // State
            $state = array(
                'id' => $stateItem->id,
                'name' => $stateItem->name,
                'slug' => str_slug($stateItem->name),
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
                    'city_id' => $cityId,
                    'name' => $cityItem->name,
                    'slug' => str_slug($cityItem->name),
                    'locale' => 'en'
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

        DB::table('cities_translations')->insert($cityDb1);
        DB::table('cities_translations')->insert($cityDb2);
        DB::table('cities_translations')->insert($cityDb3);
        DB::table('cities_translations')->insert($cityDb4);
        DB::table('cities_translations')->insert($cityDb5);
    }
}
