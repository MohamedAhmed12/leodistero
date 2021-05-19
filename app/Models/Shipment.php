<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
