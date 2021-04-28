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
            'product_group' => 'EXP', // if u don't pass it, it will take the config default value
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
           if( isset($standard->errors->Notification) || isset($express->errors->Notification)) {
            return [
                'standard' => '',
                'express' => ''
            ];
           }
        }
    }
}
