<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $table = "properties_analytics";
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id', 'click', 'view'
    ];

}
