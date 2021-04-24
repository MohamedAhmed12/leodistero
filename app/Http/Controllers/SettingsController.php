<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Settings::all();

        return view('pages.settings.index')->with(['settings'=>$settings]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('settings') && is_array($request->settings)){
            $settings =  $request->settings;
            $insert = [];
            foreach($settings as $key=>$value){
                if($value){
                    array_push($insert,[
                        'key'=>$key,
                        'value'=>$value,
                    ]);
                }
            }

            Settings::query()->delete();

            Settings::insert($insert);
        }
        session()->flash('saved',true);

        return redirect()->route('settings.index');
    }
}
