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

    
    /**
     * accessor to get the default state.
     *
     * @var array
     */
    public function getDefaultStateAttribute()
    {
        return State::whereName($this->capital)->first() ?? $this->states->first();
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
    
    public function states(){
        return $this->hasMany(State::class, 'country_id', 'id');
    }

}
