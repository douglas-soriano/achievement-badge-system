<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Tests\TestCase;

class JsonResponseTest extends TestCase
{
    /**
     * Test for the route and controller.
     */
    /** @test */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);
    }

    /**
     * Test the json structure according to the assessment rules.
     */
    /** @test */
    public function test_it_returns_user_achievements_data(): void
    {
        $this->refreshSpecificTables(['users', 'user_achievements', 'user_badges']);

        $user = User::factory()->create();

        // Achievements to test
        $achievements_to_test = ['first_lesson_watched', '5_lessons_watched', '10_lessons_watched', '25_lessons_watched'];

        // Simulate unlocking some achievements
        foreach ($achievements_to_test as $achievement) {
            $user->unlockAchievement(Achievement::where('code_name', $achievement)->first());
        }

        // Simulate unlocking some badges
        $badges = Badge::where('minimum_achievements_count', '<=', count($achievements_to_test))->orderBy('id', 'ASC')->get();
        if ($badges) {
            foreach ($badges as $badge) {
                $user->unlockBadge($badge);
            }
        }

        $response = $this->getJson("/users/{$user->id}/achievements");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaining_to_unlock_next_badge'
        ]);

        // Results test
        $achievements_names_to_test = Achievement::whereIn('code_name', $achievements_to_test)->get()->pluck('name')->toArray();
        $next_comment_achievement = Achievement::where('category', 'comments')->whereNotIn('code_name', $achievements_to_test)->orderBy('id', 'ASC')->first();
        $next_lesson_achievement = Achievement::where('category', 'lessons')->whereNotIn('code_name', $achievements_to_test)->orderBy('id', 'ASC')->first();
        $current_badge = Badge::where('minimum_achievements_count', '<=', count($achievements_to_test))->orderBy('id', 'DESC')->first();
        $next_badge = Badge::where('minimum_achievements_count', '>', count($achievements_to_test))->orderBy('id', 'ASC')->first();

        $response->assertJsonFragment([
            'unlocked_achievements' => $achievements_names_to_test,
            'next_available_achievements' => [
                $next_comment_achievement->name,
                $next_lesson_achievement->name,
            ],
            'current_badge' => $current_badge->name,
            'next_badge' => $next_badge->name,
            'remaining_to_unlock_next_badge' => ($next_badge->minimum_achievements_count - count($achievements_to_test)),
        ]);
    }

    /**
    * HELPER :: Refresh specific tables.
    */
    public function refreshSpecificTables($tables = []): void
    {
        foreach ($tables as $table) \DB::table($table)->truncate();
    }
}
