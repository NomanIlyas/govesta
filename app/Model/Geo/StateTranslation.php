<?php

namespace App\Model\Geo;

use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{
    public $timestamps = false;
    public $table = "states_translations";
    protected $fillable = ['name', 'slug', 'description', 'locale'];

}
