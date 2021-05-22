<?php

namespace App\Services;

use stdClass;
use Carbon\Traits\Date;
use App\Models\Shipment;
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
            'city' => $data['from']['capital']['name'],
            'country_code' => $data['from']['code'],
            'postal_code' => $data['from']['capital']['postal_code']
        ];

        $destinationAddress = [
            'line1' => 'Test String',
            'city' => $data['to']['capital']['name'],
            'country_code' => $data['to']['code'],
            'postal_code' => $data['to']['capital']['postal_code']
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


    public function createShipment(object $data)
    {
        $callResponse = AramexProvider::createShipment([
            'shipper' => [
                'name' => $data->shipper_name,
                'email' => $data->shipper_email,
                'phone'      => $data->shipper_number,
                'cell_phone' => $data->shipper_number,
                'country_code' => $data->shipperCountry->code,
                'city' => $data->shipperCity->name,
                'zip_code' => $data->shipperCity->postal_code,
                'line1' => $data->shipper_adress_line,
                'line2' => $data->shipper_adress_line
            ],
            'consignee' => [
                'name' => $data->recipient_name,
                'email' => $data->shipper_email,
                'phone' => $data->recipient_number,
                'cell_phone' => $data->recipient_number,
                'country_code' => $data->recipientCountry->code,
                'city' => $data->recipientCity->name,
                'zip_code' => $data->recipientCity->postal_code,
                'line1' => $data->recipient_adress_line,
                'line2' => $data->recipient_adress_line
            ],
            'shipping_date_time' => strtotime($data->package_shipping_date_time),
            'due_date' => strtotime($data->package_due_date),
            'comments' => '',
            'pickup_location' => $data->package_pickup_location,
            'pickup_guid' => null,
            'weight' => $data->package_weight,
            'length' => $data->package_length,
            'width' => $data->package_width,
            'height' => $data->package_height,
            'number_of_pieces' => 1,
            'product_type' => $data->package_shipment_type == 'standard' ? 'DPX' : 'PPX',
            'customs_value_amount' => $data->package_value, //optional (required for express shipping)
            'description' => $data->package_description ?? '',
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
            
            Shipment::whereId($data->id)->update([
                'label_path' => asset(Storage::url($labelPath)),
                'provider_shipment_id' => $shipmentId,
                'provider_status' => '',
                'status' => 5
            ]);
        } else {
            Shipment::whereId($data->id)->update([
                'provider_status' => $callResponse->errors ? $callResponse->errors[0]->Message : '',
                'status' => 2
            ]);
        }
    }
}
