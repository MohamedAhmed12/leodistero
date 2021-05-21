<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Country\CountryCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Resources\Country\Country as CountryResource;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::whereHas('cities', function (Builder $query) {
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
        $country->load([
            'cities' => function (HasMany $query) {
                $query->whereNotNull('postal_code')->get();
            }
        ]);

        return new CountryResource($country);
    }
}
