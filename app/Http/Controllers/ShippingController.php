<?php

namespace App\Http\Controllers;

use App\Services\DHL;
use App\Models\Country;
use App\Services\Fedex;
use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;

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
        $data['from'] = Country::findOrFail($data['from'])->toArray();
        $data['to'] = Country::findOrFail($data['to'])->toArray();

        $rate = [
            'dhl' => $this->dhl->calculateRate($data),
            'fedex' => $this->fedex->calculateRate($data),
        ];
        
        return response()->json($rate);
    }
}
