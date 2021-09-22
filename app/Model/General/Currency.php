<?php

namespace App\Model\General;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'symbol', 'name'
    ];

}
