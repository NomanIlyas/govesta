<?php

namespace App\Model\Geo;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use Translatable;

    public $translationModel = 'App\Model\Geo\CountryTranslation';

    public $translatedAttributes = ['name', 'slug', 'description'];

    protected $fillable = ['id', 'featured_image_id', 'code', 'code3', 'currency', 'phone_prefix'];
    
    public $timestamps = false;

    public function states()
    {
        return $this->hasMany('App\Model\Geo\State');
    }

}
