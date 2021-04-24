<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Country\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'id'              =>   $this->id,
            'name'            =>   $this->name,
            'email'           =>   $this->email,
            'phone_number'    =>   $this->phone_number,
            'company'         =>   $this->company,
            'address_line_1'  =>   $this->address_line_1,
            'address_line_2'  =>   $this->address_line_2,
            'country_id'      =>   $this->country_id,
            'city_id'         =>   $this->city_id,
            'state_id'        =>   $this->state_id,

            'zip_code'        =>   $this->zip_code,

            'id_front'        =>   $this->getFirstMediaUrl("id_front"),
            'id_back'         =>   $this->getFirstMediaUrl("id_back"),

            'created_at'      =>   $this->created_at->format('Y-m-d H:ia'),
            'updateD_at'      =>   $this->created_at->format('Y-m-d H:ia'),

            'country'         =>   new Country($this->country),
            'city'            =>   new Country($this->city),
            'state'           =>   new Country($this->state),
        ];
    }
}
