<?php

namespace App\Model\Property; 

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = "properties_images";
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
