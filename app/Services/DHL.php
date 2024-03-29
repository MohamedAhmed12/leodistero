<?php

namespace App\Services;

use DateTime;
use stdClass;
use DateTimeZone;
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
use Hamcrest\Type\IsString;

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
        $data->package_shipping_date_time = Carbon::create($data->package_shipping_date_time);
        // $data->package_shipping_date_time->setTimezone(new DateTimeZone('GMT'));

// dd($data->package_shipping_date_time->isoFormat('YYYY-MM-DD\THH:mm:SS zZ'));
// dd($data->package_shipping_date_time->format('y-m-d\TH:m:s \G\M\T z'));/
// '2010-02-11T17:10:09 GMT+01:00')
// "2021-05-28T11:58:00 UTC+00:00"
// "2021-05-28T11:58:00 UTC+00:00"
// dd("2021-05-28T11:58:00 GMT+00:00", ($data->package_shipping_date_time->isoFormat('YYYY-MM-DD\THH:mm:SS zZ')));
        $body = [
            "plannedShippingDateAndTime" =>"2021-05-28T11:58:00 GMT+00:00",
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
                        "postalCode" => $data->shipper_zip_code,
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
                        "postalCode" => $data->recipient_zip_code,
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
                        "weight" => intval($data->package_weight),
                        "dimensions" => [
                            "length" => intval($data->package_length),
                            "width" => intval($data->package_width),
                            "height" => intval($data->package_height)
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
                "declaredValue" => intval($data->package_value),
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
                'status' => 'shipped'
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
                'status' => 'pending_need_updates'
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
