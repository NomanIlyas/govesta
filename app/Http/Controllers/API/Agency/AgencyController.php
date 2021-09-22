<?php

namespace App\Http\Controllers\API\Agency;

use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AgencyController extends Controller
{
    public function status(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }
        $user = Auth::user();
        $user->agency->status = $request['status'];
        $user->agency->save();
    }
}
