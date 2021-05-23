<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Services\DHL;
use App\Models\Country;
use App\Services\Fedex;
use App\Models\Shipment;
use App\Services\Aramex;
use DHL\Datatype\AM\Request;
use App\Services\AramexAdapter;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Interfaces\ShippingAdapterInterface;
use App\Http\Requests\CreateShipmentFormRequest;

class ShipmentController extends Controller
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
        $data['from'] = Country::findOrFail($data['from'])->toArray();
        $data['to'] = Country::findOrFail($data['to'])->ToArray();
        $data['from']['capital'] =
            City::whereName($data['from']['capital'])->first() ??
            City::where(['country_id' => $data['from']['id']])->first();
        $data['to']['capital'] =
            City::whereName($data['to']['capital'])->first() ??
            City::where(['country_id' => $data['to']['id']])->first();

        $rate = [
            'dhl' => $this->dhl->calculateRate($data),
            'fedex' => $this->fedex->calculateRate($data),
            'aramex' => $this->aramex->calculateRate($data),
        ];

        return response()->json($rate);
    }

    public function createShipment(CreateShipmentFormRequest $request,  $shippingProvider)
    {
        $data = $request->validated();
        $data['provider'] = $shippingProvider;
        $shipment = Shipment::create($data);

        return response()->json($shipment);
    }
}
