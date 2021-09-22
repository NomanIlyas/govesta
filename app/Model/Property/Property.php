<?php

namespace App\Model\Property;

use App\Enums\TransactionType;
use App\Enums\Property\PropertyCategory;
use App\Enums\Property\PropertyParking;
use App\Enums\Property\PropertyStateType;
use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;
use App\Model\Property\Feature;

class Property extends Model
{

    use Translatable;

    public $translationModel = 'App\Model\Property\PropertyTranslation';

    public $translatedAttributes = ['slug', 'title', 'description', 'link'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'agency_id',
        'address_id',
        'type_id',
        'sub_type_id',
        'currency_id',
        'gallery_id',
        'price',
        'transaction_type',
        'sqm',
        'bedrooms',
        'bathrooms',
        'rooms',
        'features',
        'cpc',
        'sync_id',
        'published_at',
        'market_status',
        'year_built',
        'category',
        'type_of_state',
        'parking_type',
        'parking',
        'balconies',
        'terraces'
    ];

    protected $appends = ['features_list'];

    public function getTransactionTypeAttribute($value)
    {
        return strtolower(TransactionType::getKey($value));
    }

    public function setTransactionTypeAttribute($value)
    {
        $this->attributes['transaction_type'] = TransactionType::getValue(ucfirst($value));
    }

    public function getCategoryAttribute($value)
    {
        return strtolower(PropertyCategory::getKey($value));
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = PropertyCategory::getValue(ucfirst($value));
    }

    public function getTypeOfStateAttribute($value)
    {
        return strtolower(PropertyStateType::getKey($value));
    }

    public function setTypeOfStateAttribute($value)
    {
        $this->attributes['type_of_state'] = PropertyStateType::getValue(ucfirst($value));
    }

    public function getParkingTypeAttribute($value)
    {
        return strtolower(PropertyParking::getKey($value));
    }

    public function setParkingTypeAttribute($value)
    {
        $this->attributes['parking_type'] = PropertyParking::getValue(ucfirst($value));
    }

    public function images()
    {
        return $this->hasMany('App\Model\Property\Image')->orderBy('order');
    }

    public function floor()
    {
        return $this->hasMany('App\Model\Property\FloorPlan')->orderBy('order');
    }

    public function address()
    {
        return $this->belongsTo('App\Model\Geo\Address', 'address_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Model\Property\Type', 'type_id');
    }

    public function subType()
    {
        return $this->belongsTo('App\Model\Property\SubType', 'sub_type_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Model\General\Currency', 'currency_id');
    }

    public function agency()
    {
        return $this->belongsTo('App\Model\User\Agency', 'agency_id');
    }

    public function analytics()
    {
        return $this->hasOne('App\Model\Property\Analytics');
    }

    public function agencyCpc()
    {
        return $this->belongsTo('App\Model\User\Agency', 'agency_id')->cpc;
    }

    public function getFeaturesListAttribute()
    {
        if (isset($this->features)) {
            $ids = explode(',', $this->features);
            return Feature::whereIn('id', $ids)->get();
        }
        return [];
    }
}
