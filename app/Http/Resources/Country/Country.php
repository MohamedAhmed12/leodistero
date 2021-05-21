<?php

namespace App\Http\Resources\Country;

use DOMDocument;
use App\Http\Resources\User\User;
use App\Http\Resources\Category\Category;
use App\Http\Resources\State\StateCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Country extends JsonResource
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
            'code'       =>  $this->code,
            'states'     =>  new StateCollection($this->whenLoaded('states')),
            'created_at' =>  $this->created_at->format('Y-m-d H:ia'),
            'updateD_at' =>  $this->created_at->format('Y-m-d H:ia'),
        ];
    }
}
