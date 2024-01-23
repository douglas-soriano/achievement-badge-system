<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * The name of the factory.
     *
     * @var string
     */
    protected $name = 'achievement';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'category' => $this->faker->randomElement(['lessons', 'comments'])
            'code_name' => Str::snake($this->faker->sentence),
            'description' => $this->faker->paragraph,
        ];
    }
}
