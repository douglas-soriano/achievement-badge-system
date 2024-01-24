<?php

namespace Tests\Feature;

use App\Events\CommentWritten;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

class CommentAchievementsTest extends TestCase
{
    protected $fake_comment = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

    /**
    * Test to trigger the first comment written achievement.
    */
    /** @test */
    public function first_comment_written_achievement_unlocks_on_writing_first_comment(): void
    {
        // Refresh tables
        $this->refreshSpecificTables(['users', 'comments', 'user_achievements', 'user_badges']);

        // Create a user and achievement
        $user = User::factory()->create();
        $achievement = Achievement::where('code_name', 'first_comment_written')->first();

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Create a comment and simulate writing it
        $user->sendComment($this->fake_comment);

        // Refresh the user to ensure achievements are updated
        $user->refresh();

        // Assert that the user now has the achievement
        $this->assertCount(1, $user->achievements);
        $this->assertTrue($user->hasAchievement($achievement));
    }

    /**
    * Test to trigger the [3, 5, 10, 20] comment written achievements.
    */
    /** @test */
    public function unlock_achievements_unlocks_after_writing_n_comments(): void
    {
        // Milestones to test
        $milestones_to_test = [3, 5, 10, 20];

        // Refresh tables
        $this->refreshSpecificTables(['users', 'comments', 'user_achievements', 'user_badges']);

        // Create a user and achievement
        $user = User::factory()->create();

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Test for each milestone
        $comments_count = 1;
        foreach ($milestones_to_test as $min_comments_count) {
            // Get the achievement
            $achievement = Achievement::where('code_name', $min_comments_count . '_comments_written')->first();
            if ($achievement) {
                // Create and "watch" five comments
                for ($i = $comments_count; $i <= $min_comments_count; $i++) {
                    // Create a comment and simulate writing it
                    $user->sendComment($this->fake_comment);
                    // Increment count so we continue on creating comments
                    $comments_count = $i;
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
        $this->refreshSpecificTables(['users', 'comments', 'user_achievements', 'user_badges']);

        // Create a user, achievement, and requirements
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create([
            'name' => 'Master Writer!',
            'code_name' => 'master_writer',
            'category' => 'comments',
            'description' => 'You\'re a master writer! Wrote 5 long comments!'
        ]);
        $achievement->requirements()->createMany([
            [
                'type' => 'total_comments',
                'value' => 5,
            ], [
                'type' => 'comment_length',
                'value' => 200,
            ],
        ]);

        // Assert that the user doesn't have the achievement yet
        $this->assertEmpty($user->achievements);

        // Write several comments
        for ($i = 0; $i < 7; $i++) {
            // Create a comment and simulate writing it
            $user->sendComment($this->fake_comment . " " . $this->fake_comment);
        }

        // Refresh the user to ensure achievements are updated
        $user->refresh();

        // Assert that the user now has the achievement
        $this->assertTrue($user->achievements->contains($achievement));

        // Delete created achievement
        \DB::delete("DELETE FROM achievements WHERE code_name = 'master_writer'");
    }

    /**
    * HELPER :: Refresh specific tables.
    */
    public function refreshSpecificTables($tables = []): void
    {
        foreach ($tables as $table) \DB::table($table)->truncate();
    }
}
