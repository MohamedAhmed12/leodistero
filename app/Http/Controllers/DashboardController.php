<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Holds categories with posts count
     *
     * @var array
     */
    public $categories      = [];

    /**
     * Holds number of orders per month
     *
     * @var array
     */
    public $orders        = [];

    /**
     * Holds number of subscribers per month
     *
     * @var array
     */
    public $subscribers     = [];

    /**
     * Holds number of user per month
     *
     * @var array
     */
    public $users           = [];


    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $faker = \Faker\Factory::create();


        if(config('database.default') == 'mysql'){
            $this->orders = Order::selectRaw('COUNT(*) as count, MONTH(created_at) month')->groupBy('month')->pluck('count','month')->toArray();
            $orders = $this->buildYear($this->orders);

        }else{
            $this->orders = Order::selectRaw('COUNT(*) as count, EXTRACT(MONTH from created_at) as month')->groupBy('month')->pluck('count','month')->toArray();
            $orders = $this->buildYear($this->orders);

        }


        return view('pages.dashboard.index')->with([
            'orders'       => $orders,
        ]);
    }

    private function buildYear($data){
        $year = [];

        $year['January']       = array_key_exists(1,$data) ? $data[1] : 0;
        $year['February']      = array_key_exists(2,$data) ? $data[2] : 0;
        $year['March']         = array_key_exists(3,$data) ? $data[3] : 0;
        $year['April']         = array_key_exists(4,$data) ? $data[4] : 0;
        $year['May']           = array_key_exists(5,$data) ? $data[5] : 0;
        $year['June']          = array_key_exists(6,$data) ? $data[6] : 0;
        $year['July']          = array_key_exists(7,$data) ? $data[7] : 0;
        $year['August']        = array_key_exists(8,$data) ? $data[8] : 0;
        $year['September']     = array_key_exists(9,$data) ? $data[9] : 0;
        $year['October']       = array_key_exists(10,$data) ? $data[10] : 0;
        $year['November']      = array_key_exists(11,$data) ? $data[11] : 0;
        $year['December']      = array_key_exists(12,$data) ? $data[12] : 0;

        return $year;
    }

}
