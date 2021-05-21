<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\City\City as CityResource;
use App\Http\Resources\City\CityCollection;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = City::query();

        if($request->query('country') != null){
            $countries->with('country');
        }
        $countries = $countries->get();

        return new CityCollection($countries);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, City $country)
    {
        if($request->query('country') != null){
            $country->load('country');
        }
        return new CityResource($country);
    }
}
