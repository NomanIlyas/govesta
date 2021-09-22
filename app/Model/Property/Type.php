<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Type extends Model
{
    protected $table = "property_types";

    use Translatable;

    public $translationModel = 'App\Model\Property\TypeTranslation';

    public $translatedAttributes = ['name', 'slug'];

    protected $fillable = ['id'];


    public function subType()
    {
        return $this->hasMany('App\Model\Property\SubType');
    }

}
