<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\State\State as StateResource;
use App\Http\Resources\State\StateCollection;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $states = State::query();

        if($request->query('city') != null){
            $states->with('city');
        }

        if($request->query('country') != null){
            $states->with('city.country');
        }

        $states = $states->get();

        return new StateCollection($states);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State  $city
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, State $state)
    {
        if($request->query('city') != null){
            $state->load('city');
        }

        if($request->query('country') != null){
            $state->load('city.country');
        }

        return new StateResource($state);
    }
}
