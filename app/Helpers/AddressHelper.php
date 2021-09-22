<?php

namespace App\Helpers;


use App\Model\Geo\State;
use App\Model\Geo\City;
use App\Model\Geo\District;
use App\Enums\Status;

class AddressHelper
{

    public static function parseAddress($model, $data)
    {
        // LOCATION
        if (isset($data['country_id'])) {
            $model->google_place_id = null;
            $model->country_id = null;
            $model->city_id = null;
            $model->state_id = null;
            $model->district_id = null;
            $model->street = null;
            $model->street_number = null;
            $model->postal_code = null;
            $model->longitude = null;
            $model->latitude = null;

            if (isset($data['google_place_id'])) {
                $model->google_place_id = $data['google_place_id'];
            }

            if (isset($data['country_id'])) {
                $model->country_id = $data['country_id'];
            }

            if (isset($data['state'])) {
                $state = State::find($data['state']);
                if (isset($state)) {
                    $model->state_id = $state->id;
                }
            }

            if (isset($data['city'])) {
                $city = City::find($data['city']);
                if (isset($city)) {
                    $model->state_id = $city->state_id;
                    $model->city_id = $city->id;
                }
            }

            if (isset($data['district'])) {
                $district = District::find($data['district']);
                if (isset($district)) {
                    $model->state_id = $district->state_id;
                    $model->city_id = $district->city_id;
                    $model->district_id = $district->id;
                }
            }

            if (isset($data['street'])) {
                $model->street = $data['street'];
            }

            if (isset($data['street_number'])) {
                $model->street_number = $data['street_number'];
            }

            if (isset($data['postal_code'])) {
                $model->postal_code = $data['postal_code'];
            }

            if (isset($data['longitude'])) {
                $model->longitude = $data['longitude'];
            }

            if (isset($data['latitude'])) {
                $model->latitude = $data['latitude'];
            }
            return $model;
        }
    }

    public static function parseLocation($l)
    {
        $list = explode('--', $l);
        $item = NULL;
        $type = NULL;
        if (count($list) == 2) {
            list($state, $country) = $list;
            $type = 'state';
            $item = \DB::table('countries_translations as c')
                ->leftJoin('states', 'states.country_id', '=', 'c.country_id')
                ->leftJoin('states_translations as st', 'st.state_id', '=', 'states.id')
                ->where('c.slug', $country)
                ->where('st.slug', $state)
                ->where('states.status', Status::Enabled)
                ->select('states.id as id', \DB::raw('CONCAT(st.name, ", ", c.name) as name'))
                ->get()
                ->first();
        } else if (count($list) == 3) {
            list($city, $state, $country) = $list;
            $type = 'city';
            $item = \DB::table('countries_translations as c')
                ->leftJoin('states', 'states.country_id', '=', 'c.country_id')
                ->leftJoin('states_translations as st', 'st.state_id', '=', 'states.id')
                ->leftJoin('cities', 'cities.state_id', '=', 'states.id')
                ->leftJoin('cities_translations as ct', 'ct.city_id', '=', 'cities.id')
                ->where('c.slug', $country)
                ->where('st.slug', $state)
                ->where('ct.slug', $city)
                ->where('cities.status', Status::Enabled)
                ->select('cities.id as id', \DB::raw('CONCAT(ct.name , ", ", st.name, ", ", c.name) as name'))
                ->get()
                ->first();
        } else if (count($list) == 4) {
            list($district, $city, $state, $country) = $list;
            $type = 'district';
            $item = \DB::table('countries_translations as c')
                ->leftJoin('states', 'states.country_id', '=', 'c.country_id')
                ->leftJoin('states_translations as st', 'st.state_id', '=', 'states.id')
                ->leftJoin('cities', 'cities.state_id', '=', 'states.id')
                ->leftJoin('cities_translations as ct', 'ct.city_id', '=', 'cities.id')
                ->leftJoin('districts as d', 'd.city_id', '=', 'cities.id')
                ->where('c.slug', $country)
                ->where('st.slug', $state)
                ->where('ct.slug', $city)
                ->where('d.slug', $district)
                ->where('d.status', Status::Enabled)
                ->select('d.id as id', \DB::raw('CONCAT(d.name, ", ", ct.name , ", ", st.name, ", ", c.name) as name'))
                ->get()
                ->first();
        }
        $std = new \stdClass();
        $std->id = $item ? $item->id : null;
        $std->type = $type;
        $std->name = $item ? $item->name : null;
        return $std;
    }

    public static function handleLocationList($type, $list)
    {
        return $list->map(function ($item) use ($type) {
            $search = self::getSearchTerm($item, $type);
            return ['slug' => $search->slug, 'name' => $search->name, 'id' => $item->id, 'type' => $type];
        })->toArray();
    }

    public static function getSearchTerm($item, $type)
    {
        $search = new \stdClass();
        if ($type == 'district') {
            $search->slug = $item->slug . '--' . $item->city->slug . '--' . $item->city->state->slug . '--' . $item->city->country->slug;
            $search->name = $item->name . ', ' . $item->city->name . ', ' . $item->city->state->name . ', ' . $item->city->country->name;
        } else if ($type == 'city') {
            $search->slug = $item->slug . '--' . $item->state->slug . '--' . $item->country->slug;
            $search->name = $item->name . ', ' . $item->state->name . ', ' . $item->country->name;
        } else if ($type == 'state') {
            $search->slug = $item->slug . '--' . $item->country->slug;
            $search->name = $item->name . ', ' . $item->country->name;
        }
        return $search;
    }

    public static function getLocation($request)
    {
        $search = '';
        if ($request->district_id) {
            return self::getSearchTerm(District::find($request->district_id), 'district');
        }
        if ($request->city_id) {
            return self::getSearchTerm(City::find($request->city_id), 'city');
        }
        return $search;
    }
}
