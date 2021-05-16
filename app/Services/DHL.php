<?php

namespace App\Services;

use stdClass;
use DHL\Datatype\GB\Piece;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Datatype\GB\SpecialService;
use DHL\Entity\GB\ShipmentResponse;
use Illuminate\Support\Facades\Http;
use Mvdnbrk\Laravel\Facades\DhlParcel;
use DHL\Client\Web as WebserviceClient;
use App\Interfaces\ShippingAdapterInterface;
use App\Http\Requests\CreateShipmentFormRequest;

class DHL implements ShippingAdapterInterface
{
    public function calculateRate($data)
    {
        
        $query = $this->setQuery([
            'originCountryCode' => $data['from']['code'],
            'originCityName' => $data['from']['default_state']['name'],
            'destinationCountryCode' => $data['to']['code'],
            'destinationCityName' => $data['to']['default_state']['name'],
            'weight' => $data['weight'],
            'length' => $data['length'],
            'width' => $data['width'],
            'height' => $data['height']
        ]);

        $res = Http::withHeaders([
            'content-type' => 'application/json',
            'Authorization' => 'Basic bGVvZGlzdHJvMlVTOkleMHdDXjljQyQweg=='
        ])
        ->get('https://express.api.dhl.com/mydhlapi/test/rates', $query);

        $res = $res->json();

        $standard = new stdClass;
        $express = new stdClass;

        if (isset($res['products'])) {
            $rates = $res['products'];
            foreach ($rates as $rate) {
                if (isset($rate['productName']) && $rate['productName'] == 'EXPRESS EASY DOC') {
                    $express->CurrencyCode = $rate['totalPrice'][0]['priceCurrency'];
                    $express->Value = $rate['totalPrice'][0]['price'];
                }
                if (isset($rate['productName']) && $rate['productName'] == 'EXPRESS WORLDWIDE DOC') {
                    $standard->CurrencyCode = $rate['totalPrice'][0]['priceCurrency'];
                    $standard->Value = $rate['totalPrice'][0]['price'];
                }
            }
        }

        return [
            'standard' => $standard,
            'express' => $express
        ];
    }
    
    public function createShipment(array $requset)
    {
    }


    public function setQuery(array $query)
    {
        return [
            'accountNumber' => '849491959',
            'originCountryCode' => $query['originCountryCode'],
            'originCityName' => $query['originCityName'],
            'destinationCountryCode' => $query['destinationCountryCode'],
            'destinationCityName' => $query['destinationCityName'],
            'weight' => $query['weight'],
            'length' => $query['length'],
            'width' => $query['width'],
            'height' => $query['height'],
            'plannedShippingDate' => now()->addDays(5)->toDateString(),
            'isCustomsDeclarable' => 'false',
            'unitOfMeasurement' => 'imperial'
        ];
    }
}
