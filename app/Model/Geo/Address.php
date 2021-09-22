<?php

namespace App\Model\Geo;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'city_id',
        'state_id',
        'district_id',
        'google_place_id',
        'street',
        'street_number',
        'postal_code',
        'latitude',
        'longitude',
    ];

    public function city()
    {
        return $this->hasOne('App\Model\Geo\City', 'id', 'city_id');
    }

    public function state()
    {
        return $this->hasOne('App\Model\Geo\State', 'id', 'state_id');
    }

    public function district()
    {
        return $this->hasOne('App\Model\Geo\District', 'id', 'district_id');
    }

    public function properties()
    {
        return $this->hasMany('App\Model\Property\Property', 'address_id', 'id');
    }

}
