<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\AchievementRequirement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 *
 * DESCRIPTION:
 * The AssessmentSeeder serves ONLY as a foundation for testing our achievements and badges functionality.
 * It populates your database with our assessment data.
 *
 **/

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * 1. Create one user.
         **/
        $user = User::factory()->create();

        /**
         * 2. Create Lessons Watched Achievements.
         **/
        $achievements = [
            [
                'name' => 'First Lesson Watched',
                'description' => 'Congratulations on watching your first lesson!',
                'minimum_value' => 1,
            ], [
                'name' => '5 Lessons Watched',
                'description' => 'Keep learning! You\'ve watched 5 lessons.',
                'minimum_value' => 5,
            ], [
                'name' => '10 Lessons Watched',
                'description' => 'You\'re on a roll! 10 lessons watched already!',
                'minimum_value' => 10,
            ], [
                'name' => '25 Lessons Watched',
                'description' => 'Wow, 25 lessons watched! Keep up the good work!',
                'minimum_value' => 25,
            ], [
                'name' => '50 Lessons Watched',
                'description' => 'You\'re a learning machine! 50 lessons watched!',
                'minimum_value' => 50,
            ],
        ];

        foreach ($achievements as $achievement_data) {
            $achievement = new Achievement();
            $achievement->fill([
                'name' => $achievement_data['name'],
                'category' => 'lessons',
                'code_name' => Str::snake($achievement_data['name']),
                'description' => $achievement_data['description'],
            ]);
            $achievement->save();

            // Create requirements for lesson watched achievements
            $achievement->requirements()->create([
                'type' => 'total_lessons_watched',
                'value' => $achievement_data['minimum_value'],
            ]);
        }

        /**
         * 3. Create Comments Written Achievements.
         **/
        $achievements = [
            [
                'name' => 'First Comment Written',
                'description' => 'Welcome to the community! You wrote your first comment!',
                'minimum_value' => 1,
            ], [
                'name' => '3 Comments Written',
                'description' => 'You\'re engaging with the community! 3 comments written.',
                'minimum_value' => 3,
            ], [
                'name' => '5 Comments Written',
                'description' => 'Keep sharing your thoughts! You\'ve written 5 comments.',
                'minimum_value' => 5,
            ], [
                'name' => '10 Comments Written',
                'description' => 'You\'re an active community member! 10 comments written.',
                'minimum_value' => 10,
            ], [
                'name' => '20 Comments Written',
                'description' => 'Wow, you\'re a community star! 20 comments written!',
                'minimum_value' => 20,
            ],
        ];

        foreach ($achievements as $achievement_data) {
            $achievement = new Achievement();
            $achievement->fill([
                'name' => $achievement_data['name'],
                'category' => 'comments',
                'code_name' => Str::snake($achievement_data['name']),
                'description' => $achievement_data['description'],
            ]);
            $achievement->save();

            // Create requirements for comments written achievements
            $achievement->requirements()->create([
                'type' => 'total_comments',
                'value' => $achievement_data['minimum_value'],
            ]);
        }

        /**
         * 4. Create Badges.
         **/
        $badges = [
            [
                'name' => 'Beginner',
                'description' => 'Welcome to the learning journey!',
                'image' => null,
                'minimum_achievements_count' => 0,
            ], [
                'name' => 'Intermediate',
                'description' => 'You\'re making progress! Keep learning and engaging.',
                'image' => null,
                'minimum_achievements_count' => 4,
            ], [
                'name' => 'Advanced',
                'description' => 'You\'re on a roll! Keep up the impressive learning pace.',
                'image' => null,
                'minimum_achievements_count' => 8,
            ], [
                'name' => 'Master',
                'description' => 'Congratulations! You\'ve achieved mastery in learning and engagement.',
                'image' => null,
                'minimum_achievements_count' => 10,
            ],
        ];

        foreach ($badges as $badge_data) {
            Badge::create($badge_data);
        }
    }
}

