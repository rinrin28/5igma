<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryScore;
use App\Models\Department;
use App\Models\Expectation;
use App\Models\Satisfaction;
use App\Models\Subcategory;
use App\Models\SubcategoryScore;
use App\Models\SubResponse;

class ChartService
{
    public function calculateCategoryScores($surveyId)
    {
        $categories = Category::all();
        $departments = Department::all();

        foreach ($categories as $category) {
            foreach ($departments as $department) {
                $avgExpectation = Expectation::where('survey_id', $surveyId)
                    ->where('category_id', $category->id)
                    ->where('is_submitted', true)
                    ->whereHas('user', fn ($q) => $q->where('department_id', $department->id))
                    ->avg('score') ?: 0;

                $avgSatisfaction = Satisfaction::where('survey_id', $surveyId)
                    ->where('category_id', $category->id)
                    ->where('is_submitted', true)
                    ->whereHas('user', fn ($q) => $q->where('department_id', $department->id))
                    ->avg('score') ?: 0;

                $expectationGap = $avgExpectation - $avgSatisfaction;

                CategoryScore::updateOrCreate(
                    [
                        'survey_id' => $surveyId,
                        'category_id' => $category->id,
                        'department_id' => $department->id,
                    ],
                    [
                        'avg_expectation' => round($avgExpectation, 2),
                        'avg_satisfaction' => round($avgSatisfaction, 2),
                        'expectation_gap' => round($expectationGap, 2),
                    ]
                );
            }
        }
    }

    public function calculateSubcategoryScores($surveyId)
    {
        $subcategories = Subcategory::all();
        $departments = Department::all();

        foreach ($subcategories as $subcategory) {
            foreach ($departments as $department) {
                $avgScore = SubResponse::where('survey_id', $surveyId)
                    ->where('subcategory_id', $subcategory->id)
                    ->where('is_submitted', true)
                    ->whereHas('user', fn ($q) => $q->where('department_id', $department->id))
                    ->avg('score') ?? 0;

                SubcategoryScore::updateOrCreate(
                    [
                        'survey_id' => $surveyId,
                        'subcategory_id' => $subcategory->id,
                        'department_id' => $department->id
                    ],
                    ['avg_score' => round($avgScore, 2)]
                );
            }
        }
    }

    public function getCurrentData(int $surveyId, int $departmentId): array
    {
        return CategoryScore::where('survey_id', $surveyId)
            ->where('department_id', $departmentId)
            ->orderBy('category_id')
            ->pluck('avg_satisfaction', 'category_id')
            ->sortKeys()
            ->values()
            ->toArray();
    }

    public function getPreviousData(?int $previousSurveyId, int $departmentId): array
    {
        if (! $previousSurveyId) {
            return [];
        }

        return CategoryScore::where('survey_id', $previousSurveyId)
            ->where('department_id', $departmentId)
            ->orderBy('category_id')
            ->pluck('avg_satisfaction', 'category_id')
            ->sortKeys()
            ->values()
            ->toArray();
    }

    public function getMatrixData(int $surveyId, int $departmentId): array
    {
        return CategoryScore::where('survey_id', $surveyId)
            ->where('department_id', $departmentId)
            ->join('categories', 'category_scores.category_id', '=', 'categories.id')
            ->get(['category_scores.category_id', 'categories.name as category_name', 'category_scores.avg_expectation', 'category_scores.expectation_gap'])
            ->map(fn ($item) => [
                'label' => '設問 '.$item->category_id,
                'name' => $item->category_name,
                'x' => $item->expectation_gap,
                'y' => $item->avg_expectation,
                'r' => abs($item->expectation_gap) * $item->avg_expectation + 4,
                'color' => $item->expectation_gap < 0
                    ? 'rgba(0,255,255,0.5)'
                    : 'rgba(255,99,132,0.5)',
                'score' => ($item->expectation_gap >= 0) ? ($item->expectation_gap * $item->avg_expectation) : null,
            ])
            ->toArray();
    }

    public function getSatisfactionData(int $departmentId)
    {
        $satisfactionData = CategoryScore::selectRaw("DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date, avg(avg_satisfaction) as avg_score")
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('surveys.department_id', $departmentId)
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();

        return $satisfactionData->map(function ($score) {
            $score->avg_score = ($score->avg_score / 5) * 100;

            return $score;
        });
    }

    public function getBottleneckData($departmentId, $currentSurveyId, $previousSurveyId)
    {
        $bottleneckData = CategoryScore::selectRaw(
            "surveys.id as survey_id,
            DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date,
            SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score"
        )
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('surveys.department_id', $departmentId)
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();

        $latestBottleneckScore = CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('surveys.department_id', $departmentId)
            ->where('category_scores.survey_id', $currentSurveyId)
            ->value('score');

        $previousBottleneckScore = CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('surveys.department_id', $departmentId)
            ->where('category_scores.survey_id', $previousSurveyId)
            ->value('score');

        $bottleneckDifference = ($latestBottleneckScore !== null && $previousBottleneckScore !== null)
            ? $latestBottleneckScore - $previousBottleneckScore : null;

        return [
            'bottleneckData' => $bottleneckData->toArray(),
            'latestBottleneckScore' => $latestBottleneckScore,
            'bottleneckDifference' => $bottleneckDifference,
        ];
    }
}
