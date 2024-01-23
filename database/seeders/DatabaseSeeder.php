<?php

namespace Database\Seeders;

use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AssessmentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate 20 lessons
        $lessons = Lesson::factory()->count(20)->create();

        // Populate with the assessment rules
        $this->call(AssessmentSeeder::class);
    }
}
