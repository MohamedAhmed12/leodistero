<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Country;
use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::create([
            'name' => 'Cairo',
            'postal_code' => '11865',
            'country_id' => 1
        ]);
        State::create([
            'name' => 'Riyadh',
            'postal_code' => '11564',
            'country_id' => 2
        ]);
        State::create([
            'name' => 'Herndon',
            'postal_code' => '20171',
            'country_id' => 3
        ]);


        Country::where('id', 1)->update(['capital_id' => 1]);
        Country::where('id', 2)->update(['capital_id' => 2]);
        Country::where('id', 3)->update(['capital_id' => 3]);
    }
}
