<?php

namespace App\Http\Controllers\Api;

use App\Models\Settings;
use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\SettingsCollection;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new SettingsCollection(Settings::all());
    }
}
