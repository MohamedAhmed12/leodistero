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
        $volume = $data['length'] * $data['width'] * $data['height'];
        $totlaWeight = $volume * $data['weight'];
        if ($totlaWeight < 20) {
            return 26;
        } elseif ($totlaWeight < 200) {
            return 30;
        } elseif ($totlaWeight < 350) {
            return 38;
        } elseif ($totlaWeight < 600) {
            return 40;
        } elseif ($totlaWeight < 1450) {
            return 45;
        } elseif ($totlaWeight < 1450) {
            return 45;
        } else {
            return ($data['weight'] * 1.5) + $volume;
        }
        // dd(Http::get('https://jsonplaceholder.typicode.com/todos/1'));

        // return Http::dd()->get('api-mock.dhl.com/mydhlapi/rates');
    }
}
