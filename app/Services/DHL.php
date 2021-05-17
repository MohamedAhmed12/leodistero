<?php

namespace App\Services;

use stdClass;
use Carbon\Carbon;
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

    public function createShipment(array $data)
    {
        $body = [
            "plannedShippingDateAndTime" => $data['package']['shipping_date_time'],
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
                        "postalCode" => $data['ship_from']['zip_code'],
                        "cityName" =>  $data['ship_from']['state'],
                        "countryCode" =>  $data['ship_from']['country']['code'],
                        "provinceCode" => $data['ship_from']['country']['code'],
                        "addressLine1" => $data['ship_from']['adress_line'],
                    ],
                    "contactInformation" => [
                        "email" =>  $data['ship_from']['email'],
                        "phone" => $data['ship_from']['number'],
                        "mobilePhone" => $data['ship_from']['number'],
                        "companyName" => "Shipper Acne Co",
                        "fullName" => $data['ship_from']['name']
                    ]
                ],
                "receiverDetails" => [
                    "postalAddress" => [
                        "postalCode" => $data['ship_to']['zip_code'],
                        "cityName" =>  $data['ship_to']['state'],
                        "countryCode" =>  $data['ship_to']['country']['code'],
                        "provinceCode" => $data['ship_to']['country']['code'],
                        "addressLine1" => $data['ship_to']['adress_line'],
                    ],
                    "contactInformation" => [
                        "email" =>  $data['ship_to']['email'],
                        "phone" => $data['ship_to']['number'],
                        "mobilePhone" => $data['ship_to']['number'],
                        "companyName" => "Shipper Acne Co",
                        "fullName" => $data['ship_to']['name']
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

            return [
                'labelPath' => asset(Storage::url($labelPath)),
                'shipmentId' => $res['shipmentTrackingNumber']
            ];
        }

        return [
            'errorMsg' => $res->json('detail') ?? $res->json()
        ];
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
