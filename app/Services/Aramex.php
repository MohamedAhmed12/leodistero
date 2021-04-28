<?php

namespace App\Services;

use App\Interfaces\ShippingAdapterInterface;
use Octw\Aramex\Aramex as AramexProvider;

class Aramex implements ShippingAdapterInterface
{
    public function calculateRate($data)
    {
        $originAddress = [
            'line1' => 'Test string',
            'city' => $data['from']['capital_city']['name'],
            'country_code' => $data['from']['code'],
            'postal_code' => $data['from']['capital_city']['postal_code']
        ];

        $destinationAddress = [
            'line1' => 'Test String',
            'city' => $data['to']['capital_city']['name'],
            'country_code' => $data['to']['code'],
            'postal_code' => $data['to']['capital_city']['postal_code']
        ];

        $currency = 'USD';

        $shipmentDetailsStandard = [
            'weight' => 10, // KG so from LB to KG *0.453592
            'number_of_pieces' => 1,
            'payment_type' => 'P', // if u don't pass it, it will take the config default value 
            'product_group' => 'EXP', // if u don't pass it, it will take the config default value
            'product_type' => 'DPX', // if u don't pass it, it will take the config default value
            'height' => 5.5, // CM
            'width' => 3,  // CM
            'length' => 2.3  // CM
        ];

        $shipmentDetailsEXP = $shipmentDetailsStandard;
        $shipmentDetailsEXP['product_type'] = 'PPX';
    
        $standard = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsStandard, $currency);
        $express = AramexProvider::calculateRate($originAddress, $destinationAddress, $shipmentDetailsEXP, $currency);

        if (!isset($standard->error) && !isset($express->error)) {
            return [
                $standard->TotalAmount,
                $express->TotalAmount
            ];
        } else {
            $errors=[];
            $errors += isset($standard->errors->Notification);
            $errors += isset($express->errors->Notification);

            $error = array_map(function ($notification) {
                return $notification->Message;
            }, $errors);
            return $error;
        }
    }
}
