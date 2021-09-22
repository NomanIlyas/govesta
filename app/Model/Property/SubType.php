<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class SubType extends Model
{
    protected $table = "property_sub_types";

    use Translatable;

    public $translationModel = 'App\Model\Property\SubTypeTranslation';

    public $translatedAttributes = ['name', 'slug'];

    protected $fillable = ['type_id'];

}
