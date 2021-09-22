<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;

class FloorPlan extends Model
{
    protected $table = "properties_floor_plans";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id', 'file_id'
    ];

    public function file()
    {
        return $this->hasOne('App\Model\General\File', 'id', 'file_id');
    }

}
