<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'=>$this->faker->date(),
            'name'=>$this->faker->word(),
            'desc'=>$this->faker->text(),
            'user_id'=>1
        ];
    }
}
