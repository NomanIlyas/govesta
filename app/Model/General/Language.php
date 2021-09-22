<?php

namespace App\Model\General;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name'
    ];

}
