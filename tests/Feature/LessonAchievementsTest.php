<?php

namespace Tests\Feature;

use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonAchievementsTest extends TestCase
{

    /**
     * Test to trigger the first lesson watched achievement.
     */
    /** @test */
    public function first_lesson_watched_achievement_unlocks_on_watching_first_lesson() : void
    {
        // Refresh tables
        $this->refreshSpecificTables(['users', 'lesson_user']);

        // Create a user and achievement
        $user = User::factory()->create();
        $achievement = Achievement::where('code_name', 'first_lesson_watched')->first();

        // Assert that the user doesn't have the achievement yet
        $no_achievements = ($user->achievements && $user->achievements->contains($achievement));
        $this->assertFalse($no_achievements);

        // Create a lesson and simulate watching it
        $lesson = Lesson::inRandomOrder()->first();
        $user->lessons()->attach($lesson, ['watched' => 1]);

        // Dispatch the event simulating lesson watching
        event(new LessonWatched($lesson, $user));

        // Refresh the user to ensure achievements are updated
        $user->refresh();

        // Assert that the user now has the achievement
        $has_achievement = ($user->achievements && $user->achievements->contains($achievement));
        $this->assertTrue($has_achievement);
    }

    /**
     * HELPER :: Refresh specific tables.
     */
    public function refreshSpecificTables($tables = []) : void
    {
        foreach ($tables as $table) \DB::table($table)->truncate();
    }
}
