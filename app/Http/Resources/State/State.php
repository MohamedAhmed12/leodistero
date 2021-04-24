<?php

namespace App\Http\Resources\State;

use DOMDocument;
use App\Http\Resources\City\City;
use Illuminate\Http\Resources\Json\JsonResource;

class State extends JsonResource
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
            'city'       =>  new City($this->whenLoaded('city')),
            'created_at' =>  $this->created_at->format('Y-m-d H:ia'),
            'updateD_at' =>  $this->created_at->format('Y-m-d H:ia'),
        ];
    }
}
