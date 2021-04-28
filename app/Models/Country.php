<?php

namespace App\Models;

use App\Models\State;
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
    protected $fillable = [
        'name',
    ];

    public function cities(){
        return $this->hasMany(City::class);
    }
    
    public function defaultState(){
        return $this->hasOne(State::class, 'id', 'capital_id');
    }

}
