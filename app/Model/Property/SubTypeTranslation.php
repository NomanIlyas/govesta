<?php

namespace App\Model\Property;

use Illuminate\Database\Eloquent\Model;

class SubTypeTranslation extends Model
{
    public $timestamps = false;
    public $table = "property_sub_types_translations";
    protected $fillable = ['name', 'slug', 'locale'];

}
