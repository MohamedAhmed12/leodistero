<?php

namespace App\Http\Controllers;

use App\Services\AramexAdapter;

class ShippingController extends Controller
{
    public function calculateRate(AramexAdapter $aramex)
    {
        $rate   =[
            'aramex' => $aramex->calculateRate()
        ];
       return($rate);
    }
}
