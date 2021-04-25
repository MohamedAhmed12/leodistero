<?php

namespace App\Services;

use App\Interfaces\ShippingAdapterInterface;
use Mvdnbrk\Laravel\Facades\DhlParcel;
use DHL\Entity\GB\ShipmentResponse;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Client\Web as WebserviceClient;
use DHL\Datatype\GB\Piece;
use DHL\Datatype\GB\SpecialService;

class DHL implements ShippingAdapterInterface
{
    public function calculateRate()
    {
           }
}
