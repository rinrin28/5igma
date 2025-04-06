<?php

namespace App\Services;

use App\Models\CategoryScore;
use App\Models\SubResponse;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function getBottleneck($surveyId, $departmentId)
    {
        return CategoryScore::where('category_scores.survey_id', $surveyId)
            ->where('category_scores.department_id', $departmentId)
            ->where('category_scores.expectation_gap', '>', 0)
            ->join('categories', 'category_scores.category_id', '=', 'categories.id')
            ->get([
                'category_scores.category_id',
                'categories.question as name',
                'category_scores.expectation_gap',
                'category_scores.avg_expectation',
            ])
            ->map(function ($item) {
                return [
                    'category_id' => $item->category_id,
                    'name' => $item->name,
                    'expectation_gap' => $item->expectation_gap,
                    'avg_expectation' => $item->avg_expectation,
                    'score' => $item->expectation_gap * $item->avg_expectation,
                ];
            })
            ->sortByDesc('score')
            ->first();
    }

    public function getHistoricalData($categoryId, $departmentId)
    {
        return CategoryScore::selectRaw(
            "surveys.id as survey_id,
            DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date,
            (AVG(category_scores.avg_expectation) / 5) * 100 as avg_expectation,
            (AVG(category_scores.avg_satisfaction) / 5) * 100 as avg_satisfaction"
        )
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('category_scores.category_id', $categoryId)
            ->where('surveys.department_id', $departmentId)
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();
    }

    public function getSubcategoryScoreHistory($departmentId, $categoryId)
    {
        return SubResponse::selectRaw(
            "surveys.id as survey_id,
            DATE_FORMAT(surveys.start_date, '%Y-%m') as survey_date,
            subcategories.id as subcategory_id,
            subcategories.question as subcategory_question,
            (AVG(sub_responses.score) / 5) * 100 as avg_score"
        )
            ->join('subcategories', 'sub_responses.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->join('surveys', 'sub_responses.survey_id', '=', 'surveys.id')
            ->join('users', 'sub_responses.user_id', '=', 'users.id')
            ->where('subcategories.category_id', $categoryId)
            ->where('surveys.department_id', $departmentId)
            ->groupBy('surveys.id', 'surveys.start_date', 'subcategories.id', 'subcategories.question')
            ->orderBy('surveys.start_date')
            ->get();
    }

    public function calculateDifferences(Collection $historyData)
    {
        $latestData = $historyData->last();
        $previousData = $historyData->slice(-2, 1)->first();

        return [
            'latestExpectation' => $latestData?->avg_expectation ?? null,
            'previousExpectation' => $previousData?->avg_expectation ?? null,
            'expectationDifference' => ($latestData?->avg_expectation ?? null) - ($previousData?->avg_expectation ?? null),
            'latestSatisfaction' => $latestData?->avg_satisfaction ?? null,
            'previousSatisfaction' => $previousData?->avg_satisfaction ?? null,
            'satisfactionDifference' => ($latestData?->avg_satisfaction ?? null) - ($previousData?->avg_satisfaction ?? null),
        ];
    }

    public function getPreviousBottleneckCurrentScore($previousBottleneck, $currentSurveyId, $selectedDeptId)
    {
        if (! $previousBottleneck) {
            return null;
        }

        return CategoryScore::where('category_scores.survey_id', $currentSurveyId)
            ->where('category_scores.department_id', $selectedDeptId)
            ->where('category_scores.category_id', $previousBottleneck['category_id'])
            ->selectRaw('CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END as score')
            ->value('score');
    }
}
