<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Egypt',
            'code' => 'EG',
        ]);
        Country::create([
            'name' => 'Saudi Arabia',
            'code' => 'SA',
        ]);
        Country::create([
            'name' => 'United States',
            'code' => 'US',
        ]);

        // Country::factory(10)->create()->map(function($country){
        //     $country->cities()->saveMany(City::factory(10)->make());
        //     $country->cities->map(function($city){
        //         $city->states()->saveMany(State::factory(10)->make());
        //     });
        // });
    }
}
