<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CitiesDataTable;
use App\Models\City;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CitiesDataTable $dataTable)
    {
        return $dataTable->render('pages.states.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.states.create',[
            'cities'  =>  City::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'unique:states,name'],
            'city_id'         => ['required', 'exists:cities,id'],
        ]);

        $city = State::create($request->only(['name','city_id']));

        return redirect()->route('states.show',$city->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        return view('pages.states.show',[
            'state'=>$state
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {

        return view('pages.states.edit',[
            'state'       =>  $state,
            'cities'  =>  City::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            'name'         => ['required', 'unique:states,name,'.$state->id],
            'city_id'         => ['required', 'exists:cities,id'],
        ]);

        $state->update($request->only(['name','city_id']));

        return redirect()->route('states.show',$state->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        try{
            // $views = View::where(['post_id'=>$state->id,'type'=>'post'])->first();
            // $views->delete();
            $state->delete();
        }catch(\Exception $ex){}

        return redirect()->route('states.index');
    }
}
