<?php

namespace App\Http\Controllers;

use App\Services\DHL;
use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;

class ShippingController extends Controller
{
    private $dhl;

    public function __construct(DHL $dhl)
    {
        $this->dhl = $dhl;
    }

    public function calculateRate()
    {
        $data = request()->all();
        $rate = [
            'dhl' => $this->dhl->calculateRate($data)
        ];
        return ($rate);
    }
}
