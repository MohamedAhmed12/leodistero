<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::create([
            'name' => 'Cairo',
            'postal_code' => '11865',
            'country_id' => 1
        ]);
        City::create([
            'name' => 'Riyadh',
            'postal_code' => '11564',
            'country_id' =>2
        ]);
        

        Country::where('id', 1)->update(['capital_id' => 1]);
        Country::where('id', 2)->update(['capital_id'=> 2]);
    }
}
