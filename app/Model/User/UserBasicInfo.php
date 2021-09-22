<?php

namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;

class UserBasicInfo extends Model
{
    protected $table = "users_basic_info";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'about', 'address_id', 'phone_number', 'fax_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id'
    ];

    public function address()
    {
        return $this->belongsTo('App\Model\Geo\Address', 'address_id');
    }
}
