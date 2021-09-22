<?php

namespace App\Model\User;

use App\Model\Geo\Address;
use App\Model\User\Agency;
use App\Model\User\SocialProvider;
use App\Model\User\UserBasicInfo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable, HasRoles;

    protected $with = ['roles', 'permissions'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'email', 'password', 'avatar_id', 'cover_id', 'active', 'activation_token', 'avatar_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token'
    ];

    protected $dates = ['deleted_at'];

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst($value);
        $this->attributes['username'] = str_slug($this->first_name . ' ' . $this->last_name . ' ' . now()->timestamp, '-');
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
    }

    public function basicInfo()
    {
        return $this->hasOne('App\Model\User\UserBasicInfo');
    }

    public function agency()
    {
        return $this->hasOne('App\Model\User\Agency');
    }

    public function avatar()
    {
        return $this->belongsTo('App\Model\General\File', 'avatar_id');
    }

    public function cover()
    {
        return $this->belongsTo('App\Model\General\File', 'cover_image_id');
    }

    public static function register($request)
    {
        $request['activation_token'] = Str::random(40);
        $user = User::create($request);
        $addressId = Address::create()->id;
        UserBasicInfo::create(array(
            'user_id' => $user->id,
            'address_id' => $addressId,
        ));
        $user->assignRole($request['role']);
        if ($request['role'] == 'agency') {
            Agency::create(array(
                'user_id' => $user->id,
                'name' => ucfirst($request['company_name']),
                'slug' => str_slug($request['company_name']),
            ));
        }
        return $user;
    }
    
    public function providers()
    {
        return $this->hasMany(SocialProvider::class);
    }

}
