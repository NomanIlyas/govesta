<?php

namespace App\Helpers;

use App\Model\General\File as UploadImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\Exception\NotReadableException;

class FileHelper
{

    public static function upload($type, $uploadedFile)
    {
        if ($uploadedFile) {
            $uniqueId = uniqid() . time();
            $extension = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename = 'original.' . $extension;
            Storage::disk('s3')->putFileAs('/uploads/' . $uniqueId . '/', $uploadedFile, $filename, 'public');
            self::generateSizes($uniqueId, $type, $uploadedFile);
            $file = new UploadImage();
            $file->name = $uniqueId;
            $file->extension = $extension;
            $file->original_name = $uploadedFile->getClientOriginalName();
            $file->type = $type;
            $file->save();
            return $file;
        }
        return null;
    }

    public static function removeFile($id)
    {
        $file = UploadImage::find($id);
        if (isset($file)) {
            Storage::disk('s3')->deleteDirectory('uploads/' . $file->name);
            return $file->delete();
        }
    }

    public static function addRemoteFile($type, $url)
    {
        $file = new UploadImage();
        $file->url = $url;
        $file->type = $type;
        $file->save();
        return $file->id;
    }

    public static function generateSizes($name, $type, $file)
    {
        $sizes = config('image.sizes')[$type];
        $image = NULL;
        try {
            $image = Image::make($file);
        } catch (NotReadableException $e) {
            $image = NULL;
        }
        if (isset($image)) {
            $image->backup();
            foreach ($sizes as $size) {
                $image->fit($size['width'], $size['height']);
                $image->encode('jpg', 90);
                Storage::disk('s3')->put('/uploads/' . $name . '/' . $size['name'] . '.jpg', $image->encode(), 'public');
                $image->reset();
            }
        }
    }
}
