<?php

namespace App\Services;

use DHL\Datatype\GB\Piece;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Datatype\GB\SpecialService;
use DHL\Entity\GB\ShipmentResponse;
use Illuminate\Support\Facades\Http;
use Mvdnbrk\Laravel\Facades\DhlParcel;
use DHL\Client\Web as WebserviceClient;
use App\Interfaces\ShippingAdapterInterface;

class DHL implements ShippingAdapterInterface
{
    private $DHLToken;

    public function __construct()
    {
        // $res = Http::withHeaders([
        //     'Authorization' => 'Basic QUdoNE5FcU1oZmhGclhtSDFsUGFtaHhkb0FtbldEV0c6MzhuYWVCOW5CQ294bWxYZA=='
        // ])->get('https://api-sandbox.dhlecommerce.com/account/v1/auth/accesstoken');
        // $this->DHLToken = $res['access_token'];
    }
    public function calculateRate($data)
    {
        // $volume = $data['length'] * $data['width'] * $data['height'];
        // $totlaWeight = $volume * $data['weight'];
        // $val = 0;

        // if ($totlaWeight < 20) {
        //     $val = '26';
        // } elseif ($totlaWeight < 200) {
        //     $val = '30';
        // } elseif ($totlaWeight < 350) {
        //     $val = '38';
        // } elseif ($totlaWeight < 600) {
        //     $val = '40';
        // } elseif ($totlaWeight < 1450) {
        //     $val = '45';
        // } elseif ($totlaWeight < 1450) {
        //     $val = '45';
        // } else {
        //     $val = strval(($data['weight'] * 1.5) + $volume);
        // }

        // return [
        //     "currency" => "USD",
        //     "value" => $val
        // ];


        // dd(Http::get('https://jsonplaceholder.typicode.com/todos/1'));

        // $val= Http::dd()->get('api-mock.dhl.com/mydhlapi/rates');
    }
}
