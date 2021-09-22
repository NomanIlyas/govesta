<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use App\Model\Page\Page;
use Illuminate\Http\Request;
use Validator;

class PageController extends Controller
{

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }
        return APIResponse::success(Page::whereTranslation('slug', request('slug'))->get()->first());
    }

}
