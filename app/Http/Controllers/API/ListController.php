<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use App\Model\General\Currency;
use App\Model\General\Language;
use App\Model\Property\SubType;
use App\Model\Property\Type;
use App\Model\Property\Feature;
use Illuminate\Http\Request;
use Validator;

class ListController extends Controller
{

    public function propertyTypes()
    {
        return APIResponse::success(Type::with(['subType'])->get());
    }

    public function propertyFeatures()
    {
        return APIResponse::success(Feature::all());
    }

    public function propertySubTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_id' => 'required',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }
        return APIResponse::success(SubType::where('type_id', request('type_id'))->get());
    }

    public function languages()
    {
        return APIResponse::success(Language::all());
    }

    public function currencies()
    {
        return APIResponse::success(Currency::all());
    }

}
