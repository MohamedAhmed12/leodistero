<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'craeted_at',
        'updated_at'
    ];


    /**
     * accessor to get the default city.
     *
     * @var array
     */
    // public function getCapitalAttribute()
    // {
    //     return City::whereName($this->capital)->first() ?? $this->cities->first();
    // }

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }
}
