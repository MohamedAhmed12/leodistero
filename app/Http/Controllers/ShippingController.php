<?php

namespace App\Http\Controllers;

use App\Services\DHL;
use App\Models\Country;
use App\Services\Fedex;
use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;
use App\Services\Aramex;
use Maatwebsite\Excel\Concerns\ToArray;

class ShippingController extends Controller
{
    private $dhl;
    private $fedex;
    private $aramex;

    public function __construct(DHL $dhl, Fedex $fedex, Aramex $aramex)
    {
        $this->dhl = $dhl;
        $this->fedex = $fedex;
        $this->aramex = $aramex;
    }

    public function calculateRate()
    {
        $data = request()->all();
        $data['from'] = Country::with('defaultState')->findOrFail($data['from'])->toArray();
        $data['to'] = Country::with('defaultState')->findOrFail($data['to'])->ToArray();

        $rate = [
            'dhl' => $this->dhl->calculateRate($data),
            'fedex' => $this->fedex->calculateRate($data),
            'aramex' => $this->aramex->calculateRate($data),
        ];
        
        return response()->json($rate);
    }
}
