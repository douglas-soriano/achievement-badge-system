<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AchievementRequirement>
 */
class AchievementRequirementFactory extends Factory
{
    /**
     * The name of the factory.
     *
     * @var string
     */
    protected $name = 'achievement_requirement';

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'achievement_id' => Achievement::factory()->create()->id,
            'type' => $this->faker->randomElement(['total_lessons_watched', 'total_comments']), // e.g.: 'lesson_category', 'comment_length'
            'value' => $this->faker->randomElement([1, 3, 5, 10]),
        ];
    }
}
