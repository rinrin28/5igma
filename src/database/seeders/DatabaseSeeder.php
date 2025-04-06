<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ClientCompanySeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
            FeedbackQuestionSeeder::class,
            SurveyTypesSeeder::class,
            SurveySeeder::class,
            ScoreSeeder::class,
            FeedbackAnswerSeeder::class,
        ]);
    }
}
