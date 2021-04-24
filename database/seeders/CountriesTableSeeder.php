<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Glossary;
use App\Models\State;
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
        Country::factory(10)->create()->map(function($country){
            $country->cities()->saveMany(City::factory(10)->make());
            $country->cities->map(function($city){
                $city->states()->saveMany(State::factory(10)->make());
            });
        });
    }
}
