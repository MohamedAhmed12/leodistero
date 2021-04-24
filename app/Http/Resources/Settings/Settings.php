<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Resources\Json\JsonResource;

class Settings extends JsonResource
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
            'id'            =>  $this->id,
            'key'           =>  $this->key,
            'value'         =>  $this->value,
            'created_at'    =>  $this->created_at->format('Y-m-d H:ia'),
            'updateD_at'    =>  $this->created_at->format('Y-m-d H:ia'),
        ];
    }
}
