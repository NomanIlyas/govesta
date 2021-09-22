<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\AddressHelper;
use App\Http\Resources\APIResponse;
use App\Model\Geo\City;
use App\Model\Geo\Country;
use App\Model\Geo\District;
use App\Model\Geo\State;
use Illuminate\Http\Request;
use App\Enums\Status;

class GeoController extends Controller
{

    public function countries()
    {
        return APIResponse::success(Country::all());
    }

    public function cities()
    {
        return APIResponse::success(City::where('country_id', 82)->get());
    }

    public function districts()
    {
        $districts = District::with(['featured','city', 'city.state', 'city.country'])
        ->withCount(['address as properties'=> function ($query) {
            $query->whereHas('properties', function($q) {
                $q->where('status', Status::Published);
            });
        }])
        ->where('featured_image_id', '!=', NULL)
        ->where('status', Status::Enabled)
        ->orderBy('properties', 'DESC')
        ->limit(10)
        ->get();
        $districts = $districts->map(function($district){
            $district->search = AddressHelper::getSearchTerm($district, 'district')->slug;
            unset($district->city);
            return $district;
        });
        return APIResponse::success($districts);
    }

    public function search(Request $request)
    {
        $term = $request->term;
        $id = $request->country;
        if (!isset($term)) {
            return APIResponse::error();
        }
        $states = State::whereTranslationLike('name', "%$term%")->where('status', Status::Enabled)->with(['country'])->limit(5);
        if($id) {
            $states = $states->where('country_id', $id);
        }
        $states = $states->get();
        $states = AddressHelper::handleLocationList('state', $states);
        $cities = City::whereTranslationLike('name', "%$term%")->where('status', Status::Enabled)->with(['country', 'state'])->limit(5);
        if($id) {
            $cities = $cities->where('country_id', $id);
        }
        $cities = $cities->get();
        $cities = AddressHelper::handleLocationList('city', $cities);
        $districts = District::where('name', 'like', "%$term%")->where('status', Status::Enabled)->with(['city', 'city.state', 'city.country'])->limit(5);
        if($id) {
            $districts = $districts->where('country_id', $id);
        }
        $districts = $districts->get();
        $districts = AddressHelper::handleLocationList('district', $districts);
        return APIResponse::success(collect(array_merge($states, $cities, $districts))->slice(0, 5));
    }

    public function location(Request $request)
    {
        $type = $request->type;
        $id = $request->id;
        if ($type == 'state') {
            $state = State::find($id);
            $location = AddressHelper::getSearchTerm($state, 'state');
        }
        if ($type == 'city') {
            $city = City::find($id);
            $location = AddressHelper::getSearchTerm($city, 'city');
        }
        if ($type == 'district') {
            $district = District::find($id);
            $location = AddressHelper::getSearchTerm($district, 'district');
        }
        return APIResponse::success($location);
    }

}
