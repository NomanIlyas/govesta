<?php

namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'user_id', 'slug', 'name', 'vat', 'status', 'cpc', 'analytics_links'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = str_slug($value);
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User\User', 'user_id');
    }

    public function properties()
    {
        return $this->hasMany('App\Model\Property\Property', 'agency_id', 'id');
    }
}
 