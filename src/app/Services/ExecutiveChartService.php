<?php

namespace App\Services;

use App\Models\CategoryScore;
use App\Models\SubcategoryScore;
use App\Models\Survey;
use Carbon\Carbon;

class ExecutiveChartService
{
    /**
     * 経営層用の現在のデータを取得
     */
    public function getCurrentData(int $surveyId): array
    {
        return CategoryScore::where('survey_id', $surveyId)
            ->orderBy('category_id')
            ->pluck('avg_satisfaction', 'category_id')
            ->sortKeys()
            ->values()
            ->toArray();
    }

    /**
     * 経営層用の前回のデータを取得
     */
    public function getPreviousData(?int $previousSurveyId): array
    {
        if (!$previousSurveyId) {
            return [];
        }

        return CategoryScore::where('survey_id', $previousSurveyId)
            ->orderBy('category_id')
            ->pluck('avg_satisfaction', 'category_id')
            ->sortKeys()
            ->values()
            ->toArray();
    }

    /**
     * 経営層用のマトリックスデータを取得
     */
    public function getMatrixData(int $surveyId): array
    {
        return CategoryScore::where('survey_id', $surveyId)
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

    /**
     * 経営層用のボトルネックデータを取得
     */
    public function getBottleneckData(int $currentSurveyId, ?int $previousSurveyId): array
    {
        $bottleneckData = CategoryScore::selectRaw(
            "surveys.id as survey_id,
            DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date,
            SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score"
        )
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();

        $latestBottleneckScore = CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('category_scores.survey_id', $currentSurveyId)
            ->value('score');

        $previousBottleneckScore = $previousSurveyId ? CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('category_scores.survey_id', $previousSurveyId)
            ->value('score') : null;

        $bottleneckDifference = ($latestBottleneckScore !== null && $previousBottleneckScore !== null)
            ? $latestBottleneckScore - $previousBottleneckScore : null;

        return [
            'bottleneckData' => $bottleneckData->toArray(),
            'latestBottleneckScore' => $latestBottleneckScore,
            'bottleneckDifference' => $bottleneckDifference,
        ];
    }

    /**
     * 経営層用の満足度データを取得
     */
    public function getSatisfactionData(): array
    {
        $satisfactionData = CategoryScore::selectRaw("DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date, avg(avg_satisfaction) as avg_score")
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();

        return $satisfactionData->map(function ($score) {
            $score->avg_score = ($score->avg_score / 5) * 100;
            return $score;
        })->toArray();
    }
} 