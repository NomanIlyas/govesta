<?php

namespace App\Model\Property;

use Illuminate\Database\Eloquent\Model;

class TypeTranslation extends Model
{
    public $timestamps = false;

    public $table = "property_types_translations";

    protected $fillable = ['name', 'slug', 'locale'];

}
