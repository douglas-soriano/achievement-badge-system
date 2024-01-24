<?php

namespace Tests\Feature;

use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;

class LessonAchievementsTest extends TestCase
{

    /**
     * Test to trigger the first lesson watched achievement.
     */
    /** @test */
    public function first_lesson_watched_achievement_unlocks_on_watching_first_lesson(): void
    {
        // Refresh tables
        $this->refreshSpecificTables(['users', 'lesson_user', 'user_achievements', 'user_badges']);

        // Create a user and achievement
        $user = User::factory()->create();
        $achievement = Achievement::where('code_name', 'first_lesson_watched')->first();

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Create a lesson and simulate watching it
        $lesson = Lesson::inRandomOrder()->first();
        $user->watchLesson($lesson);

        // Refresh the user to ensure achievements are updated
        $user->refresh();

        // Assert that the user now has the achievement
        $this->assertCount(1, $user->achievements);
        $this->assertTrue($user->hasAchievement($achievement));
    }

    /**
     * Test to trigger the [5, 10, 25, 50] lesson watched achievements.
     */
    /** @test */
    public function unlock_achievements_unlocks_after_watching_n_lessons(): void
    {
        // Milestones to test
        $milestones_to_test = [5, 10, 25, 50];

        // Refresh tables
        $this->refreshSpecificTables(['users', 'lesson_user', 'user_achievements', 'user_badges']);

        // Create a user and achievement
        $user = User::factory()->create();

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Test for each milestone
        $lessons_count = 1;
        foreach ($milestones_to_test as $min_lessons_count) {
            // Get the achievement
            $achievement = Achievement::where('code_name', $min_lessons_count . '_lessons_watched')->first();
            if ($achievement) {
                // Create and "watch" five lessons
                for ($i = $lessons_count; $i <= $min_lessons_count; $i++) {
                    // Create a lesson and simulate watching it
                    $lesson = Lesson::inRandomOrder()->first();
                    $user->watchLesson($lesson);
                    // Increment count so we continue on creating lessons
                    $lessons_count = $i;
                }

                // Refresh the user to ensure achievements are updated
                $user->refresh();

                // Assert that the user now has the achievement
                $this->assertTrue($user->hasAchievement($achievement));
            }
        }

        // Assert that the user now has all achievements
        $this->assertCount((1 + count($milestones_to_test)), $user->achievements);
    }

    /**
     * Test achievements with multiple requirements to be unlocked.
     */
    /** @test */
    public function multiple_requirements_achievement_unlocks_on_meeting_all_requirements(): void
    {
        $this->refreshSpecificTables(['users', 'lesson_user', 'user_achievements', 'user_badges']);

        // Create a user, achievement, and requirements
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create([
            'name' => 'Master Learner!',
            'code_name' => 'master_learner',
            'category' => 'lessons',
            'description' => 'You\'re a master learner! Watched 5 lessons in the category Advanced!'
        ]);
        $achievement->requirements()->createMany([
            [
                'type' => 'total_lessons_watched',
                'value' => 5,
            ], [
                'type' => 'lesson_category',
                'value' => 5, // fake category ID we created on AchievementService @ meetsLessonAchievementRequirements
            ],
        ]);

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Watch several lessons, including some from the specific category
        for ($i = 0; $i < 7; $i++) {
            // Create a lesson and simulate watching it
            $lesson = Lesson::inRandomOrder()->first();
            $user->watchLesson($lesson);
        }

        // Refresh the user to ensure achievements are updated
        $user->refresh();

        // Assert that the user now has the achievement
        $this->assertTrue($user->achievements->contains($achievement));

        // Delete created achievement
        $achievement->requirements()->delete();
        $achievement->delete();
    }

    /**
     * HELPER :: Refresh specific tables.
     */
    public function refreshSpecificTables($tables = []): void
    {
        foreach ($tables as $table) \DB::table($table)->truncate();
    }
}
