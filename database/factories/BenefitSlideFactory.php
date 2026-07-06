<?php

namespace Database\Factories;

use App\Models\BenefitSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BenefitSlide>
 */
class BenefitSlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(2, true),
            'body_text' => $this->faker->paragraph(),
            'sort_order' => $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
