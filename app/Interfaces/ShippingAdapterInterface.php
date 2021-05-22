<?php

namespace App\Interfaces;

use App\Http\Requests\CreateShipmentFormRequest;

interface ShippingAdapterInterface
{
    public function calculateRate(Array $data);
    public function createShipment(object $request);    
}
