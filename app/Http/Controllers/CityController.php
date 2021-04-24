<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CitiesDataTable;
use App\Models\Country;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CitiesDataTable $dataTable)
    {
        return $dataTable->render('pages.cities.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.cities.create',[
            'countries'  =>  Country::all(),
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
            'name'         => ['required', 'unique:cities,name'],
            'country_id'         => ['required', 'exists:countries,id'],
        ]);

        $city = City::create($request->only(['name','country_id']));

        return redirect()->route('cities.show',$city->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return view('pages.cities.show',[
            'city'=>$city
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {

        return view('pages.cities.edit',[
            'city'       =>  $city,
            'countries'  =>  Country::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $request->validate([
            'name'         => ['required', 'unique:cities,name,'.$city->id],
            'country_id'         => ['required', 'exists:countries,id'],
        ]);

        $city->update($request->only(['name','country_id']));

        return redirect()->route('cities.show',$city->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        try{
            // $views = View::where(['post_id'=>$city->id,'type'=>'post'])->first();
            // $views->delete();
            $city->delete();
        }catch(\Exception $ex){}

        return redirect()->route('cities.index');
    }
}
