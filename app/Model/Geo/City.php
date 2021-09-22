<?php

namespace App\Model\Geo;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    use Translatable;

    public $timestamps = false;

    public $translationModel = 'App\Model\Geo\CityTranslation';

    public $translatedAttributes = ['name', 'slug', 'description'];

    protected $fillable = [
        'id', 'country_id', 'state_id', 'featured_image_id', 'latitude', 'longitude',
    ];

    public function state()
    {
        return $this->belongsTo('App\Model\Geo\State', 'state_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Model\Geo\Country', 'country_id');
    }

}
