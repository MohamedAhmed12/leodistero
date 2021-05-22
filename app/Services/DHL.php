<?php

namespace App\Services;

use stdClass;
use Carbon\Carbon;
use App\Models\Shipment;
use DHL\Datatype\GB\Piece;
use Illuminate\Support\Str;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Datatype\GB\SpecialService;
use DHL\Entity\GB\ShipmentResponse;
use Illuminate\Support\Facades\Http;
use Mvdnbrk\Laravel\Facades\DhlParcel;
use DHL\Client\Web as WebserviceClient;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ShippingAdapterInterface;
use App\Http\Requests\CreateShipmentFormRequest;

class DHL implements ShippingAdapterInterface
{
    public function calculateRate($data)
    {

        $query = $this->setQuery([
            'originCountryCode' => $data['from']['code'],
            'originCityName' => $data['from']['capital']['name'],
            'destinationCountryCode' => $data['to']['code'],
            'destinationCityName' => $data['to']['capital']['name'],
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

    public function createShipment(object $data)
    {
        $body = [
            "plannedShippingDateAndTime" => $data->package_shipping_date_time,
            "pickup" => [
                "isRequested" => false,
                "closeTime" => "18:00",
                "location" => "reception"
            ],
            "productCode" => "P",
            "localProductCode" => "P",
            "getRateEstimates" => false,
            "accounts" => [
                [
                    "typeCode" => "shipper",
                    "number" => "849491959"
                ]
            ],
            "customerReferences" => [
                [
                    "value" => "Customer reference",
                    "typeCode" => "CU"
                ]
            ],
            "customerDetails" => [
                "shipperDetails" => [
                    "postalAddress" => [
                        "postalCode" => $data->shipperCity->postal_code,
                        "cityName" =>  $data->shipperCity->name,
                        "countryCode" => $data->shipperCountry->code,
                        "provinceCode" => $data->shipperCountry->code,
                        "addressLine1" => $data->shipper_adress_line,
                    ],
                    "contactInformation" => [
                        "email" =>  $data->shipper_email,
                        "phone" => $data->shipper_number,
                        "mobilePhone" => $data->shipper_number,
                        "companyName" => "Shipper Co",
                        "fullName" => $data->shipper_name
                    ]
                ],
                "receiverDetails" => [
                    "postalAddress" => [
                        "postalCode" => $data->recipientCity->postal_code,
                        "cityName" =>  $data->recipientCity->name,
                        "countryCode" =>  $data->recipientCountry->code,
                        "provinceCode" => $data->recipientCountry->code,
                        "addressLine1" => $data->recipient_adress_line,
                    ],
                    "contactInformation" => [
                        "email" =>  $data->recipient_email,
                        "phone" => $data->recipient_number,
                        "mobilePhone" => $data->recipient_number,
                        "companyName" => "Shipper Co",
                        "fullName" => $data->recipient_name
                    ]
                ]
            ],
            "content" => [
                "packages" => [
                    [
                        "typeCode" => "2BP",
                        "weight" => 1,
                        "dimensions" => [
                            "length" => 1,
                            "width" => 1,
                            "height" => 1
                        ],
                        "customerReferences" => [
                            [
                                "value" => "Customer reference",
                                "typeCode" => "CU"
                            ]
                        ],
                        "description" => "Piece content description"
                    ]
                ],
                "isCustomsDeclarable" => true,
                "declaredValue" => 1,
                "declaredValueCurrency" => "USD",

                "description" => "shipment description",
                "USFilingTypeValue" => "12345",
                "incoterm" => "DDP",
                "unitOfMeasurement" => "metric"
            ],
            "requestOndemandDeliveryURL" => false,
            "getOptionalInformation" => false
        ];

        $res = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'Authorization' => 'Basic bGVvZGlzdHJvMlVTOkleMHdDXjljQyQweg=='
        ])
            ->post('https://express.api.dhl.com/mydhlapi/test/shipments', $body);

        if ($res->status() == 201) {
            $image = base64_decode($res['documents'][0]['content']);
            // dd( $image, base64_decode($image));
            $labelPath = "public/documents/" . Str::random() . '_' . time() . '.pdf';

            Storage::put(
                $labelPath,
                $image
            );

            Shipment::whereId($data->id)->update([
                'label_path' => asset(Storage::url($labelPath)),
                'provider_shipment_id' => $res['shipmentTrackingNumber'],
                'provider_status' => '',
                'status' => 5
            ]);
        } else{
            $errMsg = '';
    
            if ($res->json('detail')) {
                if ($res->json('detail') == 'Multiple problems found, see Additional Details') {
                    $errMsg = $res->json('additionalDetails')[0];
                } else {
                    $errMsg =  $res->json('detail');
                }
            }
    
            Shipment::whereId($data->id)->update([
                'provider_status' => $errMsg,
                'status' => 2
            ]);
        } 
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
