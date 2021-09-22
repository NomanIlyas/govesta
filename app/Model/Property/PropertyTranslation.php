<?php

namespace App\Model\Property;

use Illuminate\Database\Eloquent\Model;

class PropertyTranslation extends Model
{
    public $timestamps = false;
    public $table = "properties_translations";
    protected $fillable = ['title', 'slug', 'description', 'link', 'locale'];

}
