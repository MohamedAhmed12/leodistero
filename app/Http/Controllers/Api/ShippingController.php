<?php

namespace App\Http\Controllers\Api;

use App\Services\DHL;
use App\Models\Country;
use App\Services\Fedex;
use App\Services\AramexAdapter;
use App\Interfaces\ShippingAdapterInterface;
use App\Services\Aramex;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShipmentFormRequest;
use DHL\Datatype\AM\Request;

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
            // 'dhl' => $this->dhl->calculateRate($data),
            'fedex' => $this->fedex->calculateRate($data),
            'aramex' => $this->aramex->calculateRate($data),
        ];

        return response()->json($rate);
    }

    public function createShipment(CreateShipmentFormRequest $request)
    {
        $data = $request->validated();
  
        $shipmentDetails = [
            // 'dhl' => $this->dhl->createShipment($data),
            // 'fedex' => $this->fedex->createShipment($data),
            'aramex' => $this->aramex->createShipment($data),
        ];

        return response()->json($shipmentDetails);
    }
}
