<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait Images
{
    public static function update($record, $attribute, $request)
    {
        $avatar = $request[$attribute]->storeAs(
            "public/images/" . Str::plural($attribute),
            $record->id . '.png'
        );
        
        $record->update([$attribute => asset(Storage::url($avatar))]);
        
        unset($request[$attribute]);
        return $request;
    }
}
