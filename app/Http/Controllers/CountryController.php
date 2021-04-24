<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CountriesDataTable;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CountriesDataTable $dataTable)
    {
        return $dataTable->render('pages.countries.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.countries.create');
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
            'name'         => ['required', 'unique:countries,name'],
        ]);

        $country = Country::create($request->only(['name']));

        return redirect()->route('countries.show',$country->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        return view('pages.countries.show',[
            'country'=>$country
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Country $country)
    {

        return view('pages.countries.edit',[
            'country'       =>  $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name'         => ['required', 'unique:countries,name,'.$country->id],
        ]);

        $country->update($request->only(['name']));

        return redirect()->route('countries.show',$country->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        try{
            // $views = View::where(['post_id'=>$country->id,'type'=>'post'])->first();
            // $views->delete();
            $country->delete();
        }catch(\Exception $ex){}

        return redirect()->route('countries.index');
    }
}
