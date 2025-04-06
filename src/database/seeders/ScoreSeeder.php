<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryScore;
use App\Models\Expectation;
use App\Models\Satisfaction;
use App\Models\SubResponse;
use App\Models\Subcategory;
use App\Models\Survey;
use App\Models\User;
use App\Services\ChartService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2023-2024年のデータ用のユーザー（全ユーザー）
        $oldSurveys = Survey::where('survey_types_id', 1)
            ->whereYear('start_date', '<', 2025)
            ->get();
        $allUsers = User::all();
        
        // 2023-2024年のデータを作成
        foreach ($oldSurveys as $survey) {
            $this->createScoresForSurvey($survey, $allUsers);
        }

        // 2025年のデータ用のユーザー（user_id 1-8を除外）
        $newSurveys = Survey::where('survey_types_id', 1)
            ->whereYear('start_date', '2025')
            ->get();
        $filteredUsers = User::whereNotBetween('id', [1, 8])->get();
        
        // 2025年のデータを作成
        foreach ($newSurveys as $survey) {
            $this->createScoresForSurvey($survey, $filteredUsers);
        }

        // スコアの計算
        $surveyService = app(ChartService::class);
        $allSurveys = Survey::where('survey_types_id', 1)->get();
        foreach ($allSurveys as $survey) {
            $surveyService->calculateCategoryScores($survey->id);
            $surveyService->calculateSubcategoryScores($survey->id);
        }
    }

    private function createScoresForSurvey($survey, $users)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $now = Carbon::now();

        foreach ($users as $user) {
            foreach ($categories as $category) {
                Expectation::create([
                    'survey_id' => $survey->id,
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'score' => rand(1, 5),
                    'is_submitted' => true,
                    'save_at' => $now,
                ]);

                Satisfaction::create([
                    'survey_id' => $survey->id,
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'score' => rand(1, 5),
                    'is_submitted' => true,
                    'save_at' => $now,
                ]);
            }

            foreach ($subcategories as $sub) {
                SubResponse::create([
                    'survey_id' => $survey->id,
                    'subcategory_id' => $sub->id,
                    'user_id' => $user->id,
                    'score' => rand(1, 5),
                    'is_submitted' => true,
                    'save_at' => $now,
                ]);
            }
        }
    }
}
