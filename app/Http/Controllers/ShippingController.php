<?php

namespace App\Http\Controllers;

use App\Services\DHL;
use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;
use App\Services\Fedex;

class ShippingController extends Controller
{
    private $dhl;
    private $fedex;

    public function __construct(DHL $dhl, Fedex $fedex)
    {
        $this->dhl = $dhl;
        $this->fedex = $fedex;
    }

    public function calculateRate()
    {
        $data = request()->all();
        $rate = [
            'dhl' => $this->dhl->calculateRate($data),
            'fedex' => $this->fedex->calculateRate($data),
        ];
        var_dump($rate);
        return response()->json($rate);
    }
}
