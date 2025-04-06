<?php

namespace Database\Factories;

use App\Models\Subcategory;
use App\Models\SubResponse;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubResponse>
 */
class SubResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = SubResponse::class;

    public function definition(): array
    {
        return [
            'survey_id' => Survey::inRandomOrder()->first()->id,
            'subcategory_id' => Subcategory::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'score' => $this->faker->numberBetween(1, 5),
            'submitted_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
