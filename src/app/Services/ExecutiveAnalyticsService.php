<?php

namespace App\Services;

use App\Models\CategoryScore;
use App\Models\SubcategoryScore;
use App\Models\SubResponse;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExecutiveAnalyticsService
{
    /**
     * 経営層用の分析データを取得
     */
    public function getAnalytics(int $surveyId): array
    {
        // カテゴリスコアを取得
        $categoryScores = $this->calculateCategoryScores($surveyId);
        
        // 部署スコアを取得
        $departmentScores = $this->calculateDepartmentScores($surveyId);
        
        return [
            'category_scores' => $categoryScores,
            'department_scores' => $departmentScores
        ];
    }

    /**
     * カテゴリスコアを計算
     */
    private function calculateCategoryScores(int $surveyId): array
    {
        // カテゴリスコアを取得
        $scores = CategoryScore::where('survey_id', $surveyId)
            ->with('category')
            ->get();
        
        // カテゴリごとにスコアを集計
        $result = [];
        foreach ($scores as $score) {
            $result[$score->category->name] = [
                'expectation' => $score->avg_expectation,
                'satisfaction' => $score->avg_satisfaction,
                'gap' => $score->expectation_gap
            ];
        }
        
        return $result;
    }

    /**
     * 部署スコアを計算
     */
    private function calculateDepartmentScores(int $surveyId): array
    {
        // 部署スコアを取得
        $scores = DB::table('subcategory_scores')
            ->join('subcategories', 'subcategory_scores.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->join('departments', 'subcategory_scores.department_id', '=', 'departments.id')
            ->where('subcategory_scores.survey_id', $surveyId)
            ->select(
                'departments.name as department_name',
                'categories.name as category_name',
                'subcategory_scores.avg_score'
            )
            ->get();
        
        // 部署ごとにスコアを集計
        $result = [];
        foreach ($scores as $score) {
            if (!isset($result[$score->department_name])) {
                $result[$score->department_name] = [];
            }
            $result[$score->department_name][$score->category_name] = $score->avg_score;
        }
        
        return $result;
    }

    /**
     * ボトルネックを取得
     */
    public function getBottleneck($surveyId)
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return null;
        }

        // カテゴリスコアを取得
        $scores = CategoryScore::where('survey_id', $surveyId)
            ->with('category')
            ->get();

        if ($scores->isEmpty()) {
            return null;
        }

        // 期待度と満足度の差分が最も大きいカテゴリを特定
        $bottleneck = $scores->sortByDesc(function ($score) {
            return $score->avg_expectation - $score->avg_satisfaction;
        })->first();

        return [
            'category_id' => $bottleneck->category_id,
            'label' => $bottleneck->category->name,
            'name' => $bottleneck->category->name,
            'gap' => $bottleneck->avg_expectation - $bottleneck->avg_satisfaction,
            'title' => $bottleneck->category->name,
            'score' => $bottleneck->avg_expectation - $bottleneck->avg_satisfaction
        ];
    }

    /**
     * 履歴データを取得
     */
    public function getHistoricalData(int $surveyId): array
    {
        // 現在の調査を取得
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return [];
        }
        
        // 過去の調査を取得
        $previousSurveys = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();
        
        // 各調査のスコアを取得
        $result = [];
        foreach ($previousSurveys as $survey) {
            $scores = CategoryScore::where('survey_id', $survey->id)
                ->with('category')
                ->get();
            
            $surveyData = [];
            foreach ($scores as $score) {
                $surveyData[$score->category->name] = [
                    'expectation' => $score->avg_expectation,
                    'satisfaction' => $score->avg_satisfaction,
                    'gap' => $score->expectation_gap
                ];
            }
            
            $result[$survey->start_date->format('Y-m')] = $surveyData;
        }
        
        return $result;
    }

    /**
     * サブカテゴリスコアの履歴を取得
     */
    public function getSubcategoryScoreHistory(int $surveyId): array
    {
        // 現在の調査を取得
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return [];
        }
        
        // 過去の調査を取得
        $previousSurveys = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();
        
        // 各調査のサブカテゴリスコアを取得
        $result = [];
        foreach ($previousSurveys as $survey) {
            $scores = SubcategoryScore::where('survey_id', $survey->id)
                ->with(['subcategory.category', 'department'])
                ->get();
            
            $surveyData = [];
            foreach ($scores as $score) {
                $key = $score->subcategory->category->name . ' - ' . $score->subcategory->name;
                $surveyData[$key] = [
                    'department' => $score->department->name,
                    'score' => $score->avg_score
                ];
            }
            
            $result[$survey->start_date->format('Y-m')] = $surveyData;
        }
        
        return $result;
    }

    /**
     * 差分を計算
     */
    public function calculateDifferences(array $historicalData): array
    {
        $result = [];
        
        // 各カテゴリの差分を計算
        foreach ($historicalData as $date => $data) {
            if (!isset($result[$date])) {
                $result[$date] = [];
            }
            
            foreach ($data as $category => $scores) {
                $result[$date][$category] = [
                    'expectation_diff' => $scores['expectation'] - ($result[$date][$category]['expectation'] ?? 0),
                    'satisfaction_diff' => $scores['satisfaction'] - ($result[$date][$category]['satisfaction'] ?? 0)
                ];
            }
        }
        
        return $result;
    }

    /**
     * 前回のボトルネックの現在のスコアを取得
     */
    public function getPreviousBottleneckCurrentScore($surveyId)
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return null;
        }

        // 前回の調査を取得
        $previousSurvey = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSurvey) {
            return null;
        }

        // 前回のボトルネックを取得
        $previousBottleneck = $this->getBottleneck($previousSurvey->id);
        if (!$previousBottleneck) {
            return null;
        }

        // 前回のボトルネックのカテゴリIDを取得
        $categoryId = $previousBottleneck['category_id'];

        // 現在の調査で同じカテゴリのスコアを取得
        $currentScore = CategoryScore::where('survey_id', $surveyId)
            ->where('category_id', $categoryId)
            ->first();

        if (!$currentScore) {
            return null;
        }

        // 期待度と満足度の差分を計算
        return $currentScore->avg_expectation - $currentScore->avg_satisfaction;
    }

    /**
     * AI推奨事項を取得
     */
    public function getAIRecommendations(int $surveyId): ?string
    {
        // TODO: AI推奨事項の実装
        return null;
    }

    /**
     * 前回の満足度データを取得
     */
    public function getPreviousSatisfactionData(int $surveyId): array
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return array_fill(0, 16, 0);
        }

        $previousSurvey = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSurvey) {
            return array_fill(0, 16, 0);
        }

        $scores = CategoryScore::where('survey_id', $previousSurvey->id)
            ->with('category')
            ->get();

        $result = array_fill(0, 16, 0);
        foreach ($scores as $index => $score) {
            $result[$index] = $score->avg_satisfaction;
        }

        return $result;
    }

    /**
     * 現在の満足度データを取得
     */
    public function getCurrentSatisfactionData(int $surveyId): array
    {
        $scores = CategoryScore::where('survey_id', $surveyId)
            ->with('category')
            ->get();

        $result = array_fill(0, 16, 0);
        foreach ($scores as $index => $score) {
            $result[$index] = $score->avg_satisfaction;
        }

        return $result;
    }

    /**
     * 最新の満足度スコアを取得
     */
    public function getLatestSatisfactionScore(int $surveyId): float
    {
        $scores = CategoryScore::where('survey_id', $surveyId)
            ->get();

        if ($scores->isEmpty()) {
            return 0;
        }

        return $scores->avg('avg_satisfaction');
    }

    /**
     * 満足度スコアの差分を取得
     */
    public function getSatisfactionScoreDifference(int $surveyId): float
    {
        $currentScore = $this->getLatestSatisfactionScore($surveyId);
        $previousScore = $this->getPreviousSatisfactionScore($surveyId);

        return $currentScore - $previousScore;
    }

    /**
     * 前回の満足度スコアを取得
     */
    private function getPreviousSatisfactionScore(int $surveyId): float
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return 0;
        }

        $previousSurvey = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSurvey) {
            return 0;
        }

        $scores = CategoryScore::where('survey_id', $previousSurvey->id)
            ->get();

        if ($scores->isEmpty()) {
            return 0;
        }

        return $scores->avg('avg_satisfaction');
    }

    /**
     * 満足度データを取得
     */
    public function getSatisfactionData(int $surveyId): array
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return [];
        }

        $surveys = Survey::where('survey_types_id', 1)
            ->where('start_date', '<=', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        $result = [];
        foreach ($surveys as $survey) {
            $scores = CategoryScore::where('survey_id', $survey->id)
                ->get();

            if ($scores->isEmpty()) {
                continue;
            }

            $result[$survey->start_date->format('Y-m')] = $scores->avg('avg_satisfaction');
        }

        return $result;
    }

    /**
     * 最新のボトルネックスコアを取得
     */
    public function getLatestBottleneckScore($surveyId)
    {
        $bottleneck = $this->getBottleneck($surveyId);
        if (!$bottleneck) {
            return [
                'score' => 0,
                'category_id' => null,
                'name' => '該当なし',
                'label' => '該当なし'
            ];
        }
        
        return [
            'score' => $bottleneck['score'],
            'category_id' => $bottleneck['category_id'],
            'name' => $bottleneck['name'],
            'label' => $bottleneck['label']
        ];
    }

    /**
     * ボトルネックスコアの差分を取得
     */
    public function getBottleneckScoreDifference($surveyId)
    {
        $currentBottleneck = $this->getBottleneck($surveyId);
        if (!$currentBottleneck) {
            return [
                'score' => 0,
                'category_id' => null,
                'name' => '該当なし',
                'label' => '該当なし'
            ];
        }

        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return [
                'score' => 0,
                'category_id' => $currentBottleneck['category_id'],
                'name' => $currentBottleneck['name'],
                'label' => $currentBottleneck['label']
            ];
        }

        // 前回の調査を取得
        $previousSurvey = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSurvey) {
            return [
                'score' => 0,
                'category_id' => $currentBottleneck['category_id'],
                'name' => $currentBottleneck['name'],
                'label' => $currentBottleneck['label']
            ];
        }

        // 前回のボトルネックを取得
        $previousBottleneck = $this->getBottleneck($previousSurvey->id);
        if (!$previousBottleneck) {
            return [
                'score' => 0,
                'category_id' => $currentBottleneck['category_id'],
                'name' => $currentBottleneck['name'],
                'label' => $currentBottleneck['label']
            ];
        }

        // スコアの差分を計算
        $scoreDifference = $currentBottleneck['score'] - $previousBottleneck['score'];

        return [
            'score' => $scoreDifference,
            'category_id' => $currentBottleneck['category_id'],
            'name' => $currentBottleneck['name'],
            'label' => $currentBottleneck['label']
        ];
    }

    /**
     * ボトルネックデータを取得
     */
    public function getBottleneckData($surveyId)
    {
        $currentSurvey = Survey::find($surveyId);
        if (!$currentSurvey) {
            return [
                'bottleneckData' => [],
                'latestBottleneckScore' => 0,
                'bottleneckDifference' => 0
            ];
        }

        // 過去5回分の調査を取得
        $surveys = Survey::where('survey_types_id', 1)
            ->where('start_date', '<=', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        // ボトルネックデータを計算
        $bottleneckData = CategoryScore::selectRaw(
            "surveys.id as survey_id,
            DATE_FORMAT(surveys.start_date, '%Y-%m') as start_date,
            SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score"
        )
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->whereIn('surveys.id', $surveys->pluck('id'))
            ->groupBy('surveys.id', 'surveys.start_date')
            ->orderBy('surveys.start_date')
            ->get();

        // 最新のボトルネックスコアを取得
        $latestBottleneckScore = CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
            ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
            ->where('category_scores.survey_id', $surveyId)
            ->value('score') ?? 0;

        // 前回の調査を取得
        $previousSurvey = Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        // 前回のボトルネックスコアを取得
        $previousBottleneckScore = 0;
        if ($previousSurvey) {
            $previousBottleneckScore = CategoryScore::selectRaw('SUM(CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END) as score')
                ->join('surveys', 'category_scores.survey_id', '=', 'surveys.id')
                ->where('category_scores.survey_id', $previousSurvey->id)
                ->value('score') ?? 0;
        }

        // ボトルネックスコアの差分を計算
        $bottleneckDifference = $latestBottleneckScore - $previousBottleneckScore;

        return [
            'bottleneckData' => $bottleneckData->toArray(),
            'latestBottleneckScore' => $latestBottleneckScore,
            'bottleneckDifference' => $bottleneckDifference
        ];
    }

    /**
     * マトリクスデータを取得
     */
    public function getMatrixData(int $surveyId): array
    {
        $scores = CategoryScore::where('survey_id', $surveyId)
            ->with('category')
            ->get();

        $result = [];
        foreach ($scores as $score) {
            $result[] = [
                'label' => $score->category->name,
                'name' => $score->category->name,
                'expectation' => $score->avg_expectation,
                'satisfaction' => $score->avg_satisfaction,
                'gap' => $score->expectation_gap
            ];
        }

        return $result;
    }
}