<?php

namespace App\Model\Geo;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'slug', 'city_id', 'featured_image_id',
    ];

    public function address()
    {
        return $this->hasMany('App\Model\Geo\Address', 'district_id', 'id');
    }

    public function featured()
    {
        return $this->belongsTo('App\Model\General\File', 'featured_image_id');
    }

    public function city()
    {
        return $this->belongsTo('App\Model\Geo\City', 'city_id');
    }

}
