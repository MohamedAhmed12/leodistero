<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\DataTables\shipmentsDataTable;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\CreateShipmentFormRequest;
use CreateShipmentsTable;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(shipmentsDataTable $dataTable)
    {
        return $dataTable->render('pages.shipments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.shipments.create', [
            'shipments'  =>  Shipment::all(),
            'countries'  =>  Country::all(),
            'cities'  =>  City::all(),
            'providers' => ['fedex', 'dhl', 'aramex']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShipmentFormRequest $request)
    {
        $validated = $request->validated();
        $shipment = Shipment::create($validated);
        return redirect()->route('shipments.show', $shipment->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function show(Shipment $shipment)
    {
        $shipment->load([
            'shipperCountry',
            'shipperCity',
            'recipientCountry',
            'recipientCity'
        ]);
        return view('pages.shipments.show', [
            'shipment' => $shipment
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Shipment $shipment)
    {
        return view('pages.shipments.edit', [
            'shipment' =>  $shipment,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => ['required'],
        ]);

        $shipment->update($request->only(['status']));

        return redirect()->route('shipments.show', $shipment->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shipment $shipment)
    {
        try {
            $shipment->delete();
        } catch (\Exception $ex) {
        }

        return redirect()->route('shipments.index');
    }

    /**
     * Create shpment instance at provider.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $data
     * @param  string  $shippingProvider
     * @return void
     */
    public function ship($data, $shippingProvider)
    {
        app($shippingProvider)->createShipment($data);
    }
}
