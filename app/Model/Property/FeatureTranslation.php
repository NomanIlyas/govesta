<?php

namespace App\Model\Property;

use Illuminate\Database\Eloquent\Model;

class FeatureTranslation extends Model
{
    public $timestamps = false;

    public $table = "property_features_translations";

    protected $fillable = ['name', 'slug', 'locale'];

}
