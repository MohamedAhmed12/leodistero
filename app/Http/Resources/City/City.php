<?php

namespace App\Http\Resources\City;

use App\Http\Resources\User\User;
use App\Http\Resources\Category\Category;
use App\Http\Resources\Country\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class City extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         =>  $this->id,
            'name'       =>  $this->name,
            'postal_code'       =>  $this->postal_code,
            'country'    =>  new Country($this->whenLoaded('country')),
            'created_at' =>  $this->created_at->format('Y-m-d H:ia'),
            'updateD_at' =>  $this->created_at->format('Y-m-d H:ia'),
        ];
    }
}
