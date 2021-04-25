<?php

namespace App\Services;

use App\Interfaces\ShippingAdapterInterface;
use Mvdnbrk\Laravel\Facades\DhlParcel;

class Aramex implements ShippingAdapterInterface
{
    public function calculateRate()
    {
        
        $parcel = new \Mvdnbrk\DhlParcel\Resources\Parcel([
            'reference' => 'your own reference for the parcel (optional)',
            'recipient' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'street' => 'Poststraat',
                'number' => '1',
                'number_suffix' => 'A',
                'postal_code' => '1234AA',
                'city' => 'Amsterdam',
                'cc' => 'NL',
            ],
            'sender' => [
                'company_name' => 'Your Company Name',
                'street' => 'Pakketstraat',
                'additional_address_line' => 'Industrie 9999',
                'number' => '99',
                'postal_code' => '9999AA',
                'city' => 'Amsterdam',
                'cc' => 'NL',
            ],
            // Optional. This will be set as the default.
            'pieces' => [
                [
                    'parcel_type' => \Mvdnbrk\DhlParcel\Resources\Piece::PARCEL_TYPE_SMALL,
                    'quantity' => 1,
                ],
            ],
        ]);
        // dd($parcel);
        $shipment = dhlparcel()->shipments()->create($parcel);

        dd(22);
        // $params = array(
        //     'ClientInfo'              => array(
        //         // 'AccountCountryCode'    => 'JO',
        //         // 'AccountEntity'             => 'AMM',
        //         // 'AccountNumber'             => '00000',
        //         // 'AccountPin'             => '000000',
        //         'UserName'                 => env('ARAMEX_USERNAME'),
        //         'Password'                 => env('ARAMEX_PASSWORD'),
        //         'Version'                 => 'v1.0'
        //     ),

        //     'Transaction'             => array(
        //         'Reference1'            => '001'
        //     ),

        //     'OriginAddress'          => array(
        //         'City'                    => 'Amman',
        //         'CountryCode'                => 'JO'
        //     ),

        //     'DestinationAddress'     => array(
        //         'City'                    => 'Dubai',
        //         'CountryCode'            => 'AE'
        //     ),
        //     'ShipmentDetails'        => array(
        //         'PaymentType'             => 'P',
        //         'ProductGroup'             => 'EXP',
        //         'ProductType'             => 'PPX',
        //         'ActualWeight'              => array('Value' => 5, 'Unit' => 'KG'),
        //         'ChargeableWeight'          => array('Value' => 5, 'Unit' => 'KG'),
        //         'NumberOfPieces'         => 5
        //     )
        // );

        $params = array(
            'ClientInfo'              => array(
                'AccountCountryCode'    => 'GB',
                'AccountEntity'             => 'LON',
                'AccountNumber'             => '102331',
                'AccountPin'             => '321321',
                'UserName'                 => "testingapi@aramex.com",
                'Password'                 => 'R123456789$r',
                'Version'                 => 'v1'
            ),

            'Transaction'             => array(
                'Reference1'            => '001'
            ),
            'Shipments'                => array(
                'XXXXXXXXXX'
            )
        );
        // $soapClient = new \SoapClient(public_path('/static/aramex-rates-calculator-wsdl.wsdl'), array('trace' => 1));
        $soapClient = new \SoapClient(public_path('/static/shipments-tracking-api-wsdl.wsdl'), array('trace' => 1));
        $results = $soapClient->TrackShipments($params);
        return $results;
    }
}
