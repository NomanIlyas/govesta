<?php

namespace App\Helpers;

use App\Helpers\FileHelper;
use App\Model\Geo\Address;
use App\Model\Property\Image;

class PropertyHelper
{

  public static function delete($property)
  {
    $images = Image::where('property_id', $property->id)->get();
    foreach ($images as $image) {
      FileHelper::removeFile($image->file_id);
    }
    $property->delete();
    Address::where('id', $property->address_id)->delete();
    return true;
  }
}
