<?php

namespace App\Model\Geo;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use Translatable;

    public $timestamps = false;

    public $translationModel = 'App\Model\Geo\StateTranslation';

    public $translatedAttributes = ['name', 'slug', 'description'];

    protected $fillable = [
        'id', 'country_id', 'featured_image_id',
    ];

    public function country()
    {
        return $this->belongsTo('App\Model\Geo\Country', 'country_id');
    }
}
