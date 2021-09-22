<?php
namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    protected $table = "users_providers";

    protected $fillable = [
        'provider_name',
        'provider_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Model\User\User');
    }
    
}