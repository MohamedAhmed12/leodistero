<?php

namespace App\Services;

use stdClass;
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

        $standardResponse = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsStandard, $currency);
        $expressResponse = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsEXP, $currency);

        $standard = new stdClass;
        $express = new stdClass;
        if (!isset($standardResponse->error) && isset($standardResponse->TotalAmount)) {
            $standard = $standardResponse->TotalAmount;
        }
        if (!isset($expressResponse->error) && isset($expressResponse->TotalAmount)) {
            $express = $expressResponse->TotalAmount;
        }

        return [
            'standard' => $standard,
            'express' => $express
        ];
    }


    public function createShipment(array $data)
    {
        $callResponse = AramexProvider::createShipment([
            'shipper' => [
                'name' => $data['shipper']['name'],
                'email' => $data['shipper']['email'],
                'phone'      => $data['shipper']['number'],
                'cell_phone' => $data['shipper']['number'],
                'country_code' => $data['shipper']['country']['code'],
                'city' => $data['shipper']['state'],
                'zip_code' => $data['shipper']['zip_code'],
                'line1' => $data['shipper']['adress_line'],
                'line2' => $data['shipper']['adress_line'],

            ],
            'consignee' => [
                'name' => $data['recipient']['name'],
                'email' => $data['recipient']['email'],
                'phone' => $data['recipient']['number'],
                'cell_phone' => $data['recipient']['number'],
                'country_code' => $data['recipient']['country']['code'],
                'city' => $data['recipient']['state'],
                'zip_code' => $data['recipient']['zip_code'],
                'line1' => $data['recipient']['adress_line'],
                'line2' => $data['recipient']['adress_line'],
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
            'product_type' => $data['package']['shipment_type'] == 'standard' ? 'DPX' : 'PPX',
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
            return [
                'errorMsg' => $callResponse->errors ? $callResponse->errors[0]->Message : ''
            ];
        }
    }
}
