<?php

namespace App\Observers;

use Validator;
use App\Models\Shipment;
use App\Http\Controllers\ShipmentController;
use App\Http\Requests\CreateShipmentFormRequest;

class ShipmentObserver
{
    /**
     * Handle the Shipment "created" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function created(Shipment $shipment)
    {
        //
    }

    /**
     * Handle the Shipment "updated" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function updated(Shipment $shipment)
    {
        if ($shipment->status == 4) {
            $data = $shipment->load([
                'shipperCountry',
                'shipperCity',
                'recipientCountry',
                'recipientCity'
            ]);

           app(ShipmentController::class)->ship($data, $data['provider']);
        }
    }

    /**
     * Handle the Shipment "deleted" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function deleted(Shipment $shipment)
    {
        //
    }

    /**
     * Handle the Shipment "restored" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function restored(Shipment $shipment)
    {
        //
    }

    /**
     * Handle the Shipment "force deleted" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function forceDeleted(Shipment $shipment)
    {
        //
    }
}
