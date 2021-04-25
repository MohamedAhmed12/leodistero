<?php

namespace App\Http\Controllers;

use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;

class ShippingController extends Controller
{
    public function calculateRate( ShippingAdapterInterface $shippingCompany)
    {
        $rate   =[
            'aramex' => $shippingCompany->calculateRate()
        ];
       return($rate);
    }
}
