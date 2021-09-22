<?php

namespace App\Model\Geo;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    public $timestamps = false;
    public $table = "countries_translations";
    protected $fillable = ['name', 'slug', 'description', 'locale'];

}
