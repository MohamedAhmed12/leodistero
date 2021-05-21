<?php

namespace App\Models;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = ['id', 'craeted_at', 'updated_at'];

    public function getStatusAttribute($val)
    {
        return str_replace('_', ' ', $val);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function shipperCountry()
    {
        return $this->hasOne(Country::class, 'shipper_country_id', 'id');
    }

    public function shipperCity()
    {
        return $this->hasOne(City::class, 'shipper_city_id', 'id');
    }

    public function recipientCountry()
    {
        return $this->hasOne(Country::class, 'recipient_country_id', 'id');
    }

    public function recipientCity()
    {
        return $this->hasOne(City::class, 'recipient_city_id', 'id');
    }
}
