<?php

namespace App\Http\Controllers\API;

use App\Enums\Status;
use App\Helpers\AddressHelper;
use App\Helpers\PropertyHelper;
use App\Helpers\FileHelper;
use App\Http\Controllers\API\GeoController;
use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use App\Model\Geo\Address;
use App\Model\Property\Property;
use App\Model\Property\Analytics;
use App\Model\Property\Image;
use App\Model\User\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Enums\AgencyStatus;
use Carbon\Carbon;
use Lang;
use App\Enums\MarketStatus;

class PropertyController extends Controller
{

    public function addOrEdit(Request $request)
    {

        $data = $request->all();
        $user = Auth::user();

        $validator = Validator::make($data, [
            'id' => 'nullable|numeric',
            'agency_id' => 'nullable|numeric',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'currency_id' => 'nullable|numeric',
            'transaction_type' => 'nullable|string|in:buy,rent,lease',
            'sqm' => 'nullable|numeric',
            'bedrooms' => 'nullable|numeric',
            'bathrooms' => 'nullable|numeric',
            'rooms' => 'nullable|numeric',
            'type_id' => 'nullable|numeric',
            'sub_type_id' => 'nullable|numeric',
            'country_id' => 'nullable|numeric',
            'city' => 'nullable|numeric',
            'state' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'street' => 'nullable|string',
            'street_number' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'google_place_id' => 'nullable|string',
            'images' => 'nullable|array',
            'features' => 'nullable|string',
            'locale' => 'nullable|string',
            'delete_locale' => 'nullable|string',
            'year_built' => 'nullable',
            'category' => 'nullable|string',
            'type_of_state' => 'nullable|string',
            'parking_type' => 'nullable|string',
            'parking' => 'nullable|numeric',
            'balconies' => 'nullable|numeric',
            'terraces' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $property = new Property();

        $locale = $data['locale'] ?? 'en';

        if (isset($data['id'])) {
            $property = Property::with(['address'])->find($data['id']);
            $address = $property->address ?: new Address();
            $agency = Auth::user()->agency;
            if ($agency->id != $property->agency_id) {
                return APIResponse::error('differentproperty', 401);
            }
        } else {
            $agency = Agency::where(array('user_id' => $user->id))->first();
            $address = new Address();
            $property->agency_id = $agency->id;
        }

        if (isset($data['status'])) {
            $property->status = $data['status'];
            if (Status::Published ==  $property->status) {
                $property->published_at = Carbon::now();
            }
        }

        if (isset($data['title'])) {
            $property->translateOrNew($locale)->title = $data['title'];
            $property->translateOrNew($locale)->slug = str_slug($data['title']);
        }

        if (isset($data['description'])) {
            $property->translateOrNew($locale)->description = $data['description'];
        }

        if (isset($data['link'])) {
            $property->translateOrNew($locale)->link = $data['link'];
        }

        if (array_key_exists('price', $data)) {
            $property->price = $data['price'];

            if (isset($data['currency_id'])) {
                $property->currency_id = $data['currency_id'];
            }
        }

        if (isset($data['transaction_type'])) {
            $property->transaction_type = $data['transaction_type'];
        }

        if (isset($data['sqm'])) {
            $property->sqm = $data['sqm'];
        }

        if (isset($data['bedrooms'])) {
            $property->bedrooms = $data['bedrooms'];
        }

        if (isset($data['bathrooms'])) {
            $property->bathrooms = $data['bathrooms'];
        }

        if (isset($data['rooms'])) {
            $property->rooms = $data['rooms'];
        }

        if ($request->has('features')) {
            $property->features = $data['features'];
        }

        if (isset($data['type_id'])) {
            $property->type_id = $data['type_id'];
        }

        if (isset($data['sub_type_id'])) {
            $property->sub_type_id = $data['sub_type_id'];
        }

        if (isset($data['price_on_request'])) {
            $property->price_on_request = $data['price_on_request'];
        }

        if ($request->has('year_built')) {
            $property->year_built = $data['year_built'];
        }

        if (isset($data['category'])) {
            $property->category = $data['category'];
        }

        if (isset($data['type_of_state'])) {
            $property->type_of_state = $data['type_of_state'];
        }

        if (isset($data['parking_type'])) {
            $property->parking_type = $data['parking_type'];
        }

        if (isset($data['parking'])) {
            $property->parking = $data['parking'];
        }

        if (isset($data['balconies'])) {
            $property->balconies = $data['balconies'];
        }

        if (isset($data['terraces'])) {
            $property->terraces = $data['terraces'];
        }

        // Address
        if (isset($data['country_id'])) {
            $address = AddressHelper::parseAddress($address, $data);
            $address->save();
            $property->address_id = $address->id;
        }

        // Address
        if (isset($data['market_status'])) {
            $property->market_status = $data['market_status'];
        }

        if (isset($data['delete_locale'])) {
            $property->deleteTranslations($data['delete_locale']);
        }

        // Save
        $property->save();

        // Remote Image
        if (isset($data['images'])) {
            $images = $data['images'];
            foreach ($images as $image) {
                $fileId = FileHelper::addRemoteFile('property', $image);
                $file = new Image();
                $file->property_id = $property->id;
                $file->file_id = $fileId;
                $file->save();
            }
        }

        // Init Analytics
        if (!isset($data['id'])) {
            Analytics::create(['property_id' => $property->id]);
        }

        return APIResponse::success($property->id);
    }

    function list()
    {
        $agency = Auth::user()->agency;
        return APIResponse::success(
            Property::with(['images', 'floor', 'floor.file', 'images.file', 'analytics', 'address', 'address.city', 'address.state', 'address.district'])
                ->where('agency_id', $agency->id)
                ->whereIn('status', [Status::Draft, Status::Published])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function get($id)
    {
        if (!isset($id)) {
            return APIResponse::error("empty", 401);
        }
        $agency = Auth::user()->agency;
        $property = Property::with(['address', 'address.city', 'address.state', 'address.district', 'images', 'images.file', 'floor', 'floor.file'])->where('id', $id)->where('agency_id', $agency->id)->first();
        if ($property) {
            return APIResponse::success($property);
        }
        return APIResponse::error(null, 404);
    }

    public function getPublic($id)
    {
        if (!isset($id)) {
            return APIResponse::error("empty", 401);
        }
        $property = Property::with(['agency', 'address', 'analytics', 'address.city', 'address.state', 'currency', 'address.district', 'images', 'images.file']);
        if (is_numeric($id)) {
            $property->where('id', $id);
        } else {
            $strings = explode("-", $id);
            $id = $strings[count($strings) - 1];
            $property->where('id', $id);
        }
        if ($property) {
            return APIResponse::success($property->first());
        }
        return APIResponse::error(null, 404);
    }

    public function delete($id)
    {
        if (!isset($id)) {
            return APIResponse::error("empty", 401);
        }
        $agency = Auth::user()->agency;
        $property = Property::where('id', $id)->where('agency_id', $agency->id)->first();
        if ($property) {
            $property->status = Status::Deleted;
            $property->save();
            return APIResponse::success(true);
        }
        return APIResponse::error(null, 404);
    }

    public function search(Request $request, GeoController $geo)
    {
        $query = Property::with(['address', 'address.city', 'address.state', 'address.district', 'currency', 'type', 'subType', 'images', 'images.file', 'agency'])->where('status', Status::Published)
            ->whereHas('agency', function ($q) {
                $q->where('status', '=', AgencyStatus::Active);
            })->whereHas('address.state', function ($q) {
                $q->where('status', '=', Status::Enabled);
            })->whereHas('address.city', function ($q) {
                $q->where('status', '=', Status::Enabled);
            })->whereHas('address.district', function ($q) {
                $q->where('status', '=', Status::Enabled);
            })->withCount(['agency as cpc' => function ($query) {
                $query->select(\DB::raw('max(cpc)'));
            }])->withCount(['analytics as clickout' => function ($query) {
                $query->select(\DB::raw('max(clickout)'));
            }])->translatedIn(\Lang::getLocale());
        $locationTerm = "";
        $sold = 0;
        if ($request->location) {
            $parsed = AddressHelper::parseLocation($request->location);
            if ($parsed->id) {
                $query->whereHas("address", function ($q) use ($parsed) {
                    $q->where($parsed->type . '_id', "=", $parsed->id);
                });
                $locationTerm = ['slug' => $request->location, 'name' => $parsed->name];

                $query2 = Property::with(['address', 'agency'])->where('status', Status::Published)->whereHas('agency', function ($q) {
                    $q->where('status', '=', AgencyStatus::Active);
                });
                $query2->whereHas("address", function ($q) use ($parsed) {
                    $q->where($parsed->type . '_id', "=", $parsed->id);
                })->get();
                $sold = collect($query2->get())->where('market_status', MarketStatus::Sold)->count();
            }
        }
        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }
        if ($request->type) {
            $query->where('type_id', $request->type);
        }
        if ($request->sub_type) {
            $query->where('sub_type_id', $request->sub_type);
        }
        if ($request->rooms) {
            $query->where('rooms', '>=', $request->rooms);
        }
        if ($request->bedrooms) {
            $query->where('bedrooms', $request->bedrooms);
        }
        if ($request->bathrooms) {
            $query->where('bathrooms', $request->bathrooms);
        }
        if ($request->sqm) {
            $query->whereBetween('sqm', explode(':', $request->sqm));
        }
        if ($request->price) {
            $query->whereBetween('price', explode(':', $request->price));
        }
        if ($request->city_id) {
            $query->whereHas("address", function ($q) use ($request) {
                $q->where('city_id', "=", $request->city_id);
            })->get();
        }
        if ($request->district_id) {
            $query->whereHas("address", function ($q) use ($request) {
                $q->where('district_id', "=", $request->district_id);
            })->get();
        }
        if ($request->city_id || $request->district_id) {
            $locationTerm = AddressHelper::getLocation($request);
        }
        if ($request->sorting) {
            $sorting = $request->sorting;
            if ($sorting == 'date') {
                $query->orderBy('created_at', 'desc');
            } else if ($sorting == 'price') {
                $query->orderBy('price', 'ASC');
            } else if ($sorting == 'sqmprice') {
                $query->orderBy(\DB::raw("`price` / `sqm`"), 'asc');
            } else if ($sorting == 'standard') {
                $query->orderBy('published_at', 'desc');
                $query->orderBy('cpc', 'desc');
            }
        }
        $perpage = $request->limit ? $request->limit : 20;
        $total = $query->count();
        $page = $request->p ? $request->p : 1;
        $query->skip(($page - 1) * $perpage)->take($perpage);
        return APIResponse::success(array("list" => $query->get(), "location" => $locationTerm, "total" => $total, "sold" => $sold));
    }

    public function analytics(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'clickout' => 'bool|nullable',
            'view' => 'bool|nullable'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $analytics = Analytics::where('property_id', $request->id)->first();

        if (isset($request->clickout)) {
            $analytics->increment('clickout');
        }

        if (isset($request->view)) {
            $analytics->increment('view');
        }

        $analytics->save();

        return APIResponse::success($analytics);
    }/* 

    public function test2()
    {
        $list = Property::all();
        foreach ($list as $p) {
            $property = Property::find($p->id);
            $property->translateOrNew('en')->link = $p->link;
            $property->save();
        }
    } */

    /* public function disableTest()
    {
        $list = Property::all();
        foreach ($list as $p) {
            $property = Property::find($p->id);
            $translations = $property->translations;
            $linkExist = true;
            foreach ($translations as $translation) {
                if (!isset($translation->link)) {
                    $linkExist = false;
                }
            }
            if (!$linkExist) {
                $property->status = Status::Draft;
                $property->save();
            }
        }
    }

    public function moveImages()
    {
        $list = Property::all();
        foreach ($list as $p) {
            $property = Property::find($p->id);
            $translations = $property->translations;
            $linkExist = true;
            foreach ($translations as $translation) {
                if (!isset($translation->link)) {
                    $linkExist = false;
                }
            }
            if (!$linkExist) {
                $property->status = Status::Draft;
                $property->save();
            }
        }
    } */
}
