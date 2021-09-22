<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Feature extends Model
{
    protected $table = "property_features";

    use Translatable;

    public $translationModel = 'App\Model\Property\FeatureTranslation';

    public $translatedAttributes = ['name', 'slug'];

    protected $fillable = ['id'];

}
