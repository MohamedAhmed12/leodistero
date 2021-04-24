<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Country as CountryResource;
use App\Http\Resources\Country\CountryCollection;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::query();

        if($request->query('cities') != null){
            $countries->with('cities');
        }

        if($request->query('states') != null){
            $countries->with('cities.states');
        }

        $countries = $countries->get();

        return new CountryCollection($countries);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Country $country)
    {
        if($request->query('cities') != null){
            $country->load('cities');
        }

        if($request->query('states') != null){
            $country->load('cities.states');
        }

        return new CountryResource($country);
    }
}
