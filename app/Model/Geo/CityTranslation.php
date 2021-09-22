<?php

namespace App\Model\Geo;

use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    public $timestamps = false;
    public $table = "cities_translations";
    protected $fillable = ['name', 'slug', 'description', 'locale'];

}
