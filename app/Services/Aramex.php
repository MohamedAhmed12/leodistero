<?php

namespace App\Services;

use Carbon\Traits\Date;
use Illuminate\Support\Facades\Storage;
use Octw\Aramex\Aramex as AramexProvider;
use App\Interfaces\ShippingAdapterInterface;
use App\Http\Requests\CreateShipmentFormRequest;

class Aramex implements ShippingAdapterInterface
{
    public function calculateRate($data)
    {
        $originAddress = [
            'line1' => 'Test string',
            'city' => $data['from']['default_state']['name'],
            'country_code' => $data['from']['code'],
            'postal_code' => $data['from']['default_state']['postal_code']
        ];

        $destinationAddress = [
            'line1' => 'Test String',
            'city' => $data['to']['default_state']['name'],
            'country_code' => $data['to']['code'],
            'postal_code' => $data['to']['default_state']['postal_code']
        ];

        $currency = 'USD';

        $shipmentDetailsStandard = [
            'weight' => $data['weight'] * 0.453592, // KG so from LB to KG *0.453592
            'number_of_pieces' => 1,
            'payment_type' => 'P', // if u don't pass it, it will take the config default value 
            'product_type' => 'DPX', // if u don't pass it, it will take the config default value
            'height' => $data['height'] * 2.54, // CM
            'width' => $data['width'] * 2.54,  // CM
            'length' => $data['length']  * 2.54 // CM
        ];

        $shipmentDetailsEXP = $shipmentDetailsStandard;
        $shipmentDetailsEXP['product_type'] = 'PPX';

        $standard = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsStandard, $currency);
        $express = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsEXP, $currency);

        if (!isset($standard->error) && !isset($express->error)) {
            return [
                'standard' => $standard->TotalAmount,
                'express' => $express->TotalAmount
            ];
        } else {
            if (isset($standard->errors->Notification) || isset($express->errors->Notification)) {
                return [
                    'standard' => '',
                    'express' => ''
                ];
            }
        }
    }


    public function createShipment(array $data)
    {
        $callResponse = AramexProvider::createShipment([
            'shipper' => [
                'name' => $data['ship_from']['name'],
                'email' => $data['ship_from']['email'],
                'phone'      => $data['ship_from']['number'],
                'cell_phone' => $data['ship_from']['number'],
                'country_code' => $data['ship_from']['country']['code'],
                'city' => $data['ship_from']['state'],
                'zip_code' => $data['ship_from']['zip_code'],
                'line1' => $data['ship_from']['adress_line'],
                'line2' => $data['ship_from']['adress_line'],

            ],
            'consignee' => [
                'name' => $data['ship_to']['name'],
                'email' => $data['ship_to']['email'],
                'phone' => $data['ship_to']['number'],
                'cell_phone' => $data['ship_to']['number'],
                'country_code' => $data['ship_to']['country']['code'],
                'city' => $data['ship_to']['state'],
                'zip_code' => $data['ship_to']['zip_code'],
                'line1' => $data['ship_to']['adress_line'],
                'line2' => $data['ship_to']['adress_line'],
                // 'line1' => 'Line1 Details',
            ],

            'shipping_date_time' => strtotime($data['package']['shipping_date_time']),
            'due_date' => strtotime($data['package']['due_date']),
            'comments' => '',
            'pickup_location' => $data['package']['pickup_location'],
            'pickup_guid' => null,
            'weight' => $data['package']['weight'],
            'length' => $data['package']['length'],
            'width' => $data['package']['width'],
            'height' => $data['package']['height'],
            'number_of_pieces' => 1,
            'customs_value_amount' => $data['package']['value'], //optional (required for express shipping)
            'description' => $data['description'] ?? '',
        ]);
        if (empty($callResponse->error)) {
            $imageURL = $callResponse->Shipments->ProcessedShipment->ShipmentLabel->LabelURL;
            $image = file_get_contents($imageURL);
            $labelPath = "public/documents/" . substr($imageURL, strrpos($imageURL, '/') + 1);
            Storage::put(
                $labelPath,
                $image
            );

            $shipmentId = $callResponse->Shipments->ProcessedShipment->ID;

            return [
                'labelPath' => asset(Storage::url($labelPath)),
                'shipmentId' => $shipmentId
            ];
        } else {
            // foreach ($callResponse->errors as $errorObject) {
            //     dd(22, $errorObject);
            //     handleError($errorObject->Code, $errorObject->Message);
            // }
        }
    }
}
