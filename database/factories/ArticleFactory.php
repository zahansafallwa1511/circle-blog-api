<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;

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
            'title' => $this->faker->words(5, true),
            'description' => $this->faker->words(20, true),
            'author_id' => User::factory()->create()->id,
        ];
    }
}
