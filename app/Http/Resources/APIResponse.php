<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class APIResponse extends JsonResource
{

    public static function success($data = true, $message = "")
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], 200);
    }

    public static function error($message = "", $code = 400)
    {
        return response()->json([
            'error' => true,
            'message' => $message
        ], $code);
    }

}