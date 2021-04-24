<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\order;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class orderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'=>$this->faker->sentence(5),
            'body'=>$this->faker->paragraph(20),
            'excerpt'=>$this->faker->sentence(12),
            'author_id'=>User::factory()->create()->id,
            'created_at'=>$this->faker->dateTimeThisYear('+1 year'),
        ];
    }
}
