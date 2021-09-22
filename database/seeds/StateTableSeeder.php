<?php

use Illuminate\Database\Seeder;

class StateTableSeeder extends Seeder
{
    public function run()
    {
        $states = json_decode(file_get_contents(storage_path("app/json/states.json")))->states;
        $stateDb = [];

        foreach ($states as $stateItem) {

            // State
            $state = array(
                'state_id' => $stateItem->id,
                'name' => $stateItem->name,
                'slug' => str_slug($stateItem->name),
                'locale' => 'en'
            );
            $stateDb[$stateItem->id] = $state;
        }
        DB::table('states_translations')->insert($stateDb);
    }
}
