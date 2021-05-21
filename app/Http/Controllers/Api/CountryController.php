<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Country as CountryResource;
use App\Http\Resources\Country\CountryCollection;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
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
        $countries = Country::whereHas('states', function (Builder $query) {
            $query->whereNotNull('postal_code');
        })->get();

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
        $country->load('states');

        return new CountryResource($country);
    }
}
