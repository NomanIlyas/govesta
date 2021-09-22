<?php

namespace App\Http\Controllers\API;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use App\Model\Property\Analytics;
use App\Model\Property\Image;
use App\Model\Property\FloorPlan;
use App\Model\Property\Property;
use App\Model\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

  public function uploadImage(Request $request)
  {
    $images = $request->images;
    $order = $request->order ? ($request->order + 1) : 0;
    $propertyId = $request->id;
    $type = $request->type;
    if (!isset($propertyId)) {
      $user = Auth::user();
      $userModel = User::with(['agency'])->find($user->id);
      $propertyId = Property::create(['agency_id' => $userModel->agency->id])->id;
      Analytics::create(['property_id' => $propertyId]);
    }
    $ids = [];
    foreach ($images as $image) {
      $f = FileHelper::upload($type === 'property' ? 'property' : 'property-floor-plan', $image);
      if (isset($f->id)) {
        $file = $type === 'property' ? (new Image()) : (new FloorPlan());
        $file->property_id = $propertyId;
        $file->file_id = $f->id;
        $file->order = $order;
        $file->save();
        $ids[] = $f->id;
        $order++;
      }
    }

    return response()->json(
      array(
        "id" => $propertyId,
        "ids" => $ids
      )
    );
  }

  public function orderImage(Request $request)
  {
    $ids = $request->ids;
    $type = $request->type;
    if (isset($ids)) {
      foreach ($ids as $i => $id) {
        $file = $type === 'property' ? (Image::find($id)) : (FloorPlan::find($id));
        $file->order = $i;
        $file->save();
      }
    }
    return response()->json($ids);
  }

  public function uploadProfile(Request $request)
  {
    $type = $request->type;
    $f = FileHelper::upload($type, $request->file);
    $user = Auth::user();
    $deletedId = null;
    if (isset($f->id)) {
      $userModel = User::find($user->id);
      if ($type == 'user-avatar') {
        $deletedId = $userModel->avatar_id;
        $userModel->avatar_id = $f->id;
      } else if ($type == 'user-cover') {
        $deletedId = $userModel->cover_image_id;
        $userModel->cover_image_id = $f->id;
      }
      $userModel->save();
      if (isset($deletedId)) {
        FileHelper::removeFile($deletedId);
      }
    }
    return APIResponse::success($f);
  }

  public function removeImage(Request $request)
  {
    $id = $request->id;
    if (isset($id)) {
      FileHelper::removeFile($id);
    }
    return response()->json('Successfully removed');
  }
}
