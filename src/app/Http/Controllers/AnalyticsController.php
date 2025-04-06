<?php

namespace App\Http\Controllers;

use App\Models\CategoryScore;
use App\Models\Department;
use App\Services\AnalyticsService;
use App\Services\SurveyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyResult;
use App\Models\SubcategoryScore;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function showAnalytics(Request $request, SurveyService $surveyService)
    {
        $user = Auth::user();
        $selectedDeptId = $request->get('dept_id', $user->department_id);
        $currentYear = $request->get('year', date('Y'));
        $currentPeriod = $request->get('period', 'first');
        
        // 利用可能な期間を取得
        $availablePeriods = $surveyService->getAvailablePeriods($currentYear, $selectedDeptId);
        
        // 利用可能な期間がない場合は、デフォルトで上半期を指定
        if (!$currentPeriod || empty($availablePeriods) || !collect($availablePeriods)->pluck('value')->contains($currentPeriod)) {
            $currentPeriod = 'first';
        }

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($selectedDeptId, $currentYear, $currentPeriod);
        $currentSurveyId = $currentSurvey?->id;
        $previousSurveyId = $surveyService->previousSurveyId($currentSurveyId, $selectedDeptId);

        $departments = Department::orderBy('id')->get();
        $currentDeptId = $selectedDeptId;

        $currentBottleneck = $this->analyticsService->getBottleneck($currentSurveyId, $selectedDeptId);
        $previousBottleneck = $this->analyticsService->getBottleneck($previousSurveyId, $selectedDeptId);
        $previousBottleneckCurrentScore = null;

        if ($previousBottleneck) {
            $previousBottleneckCurrentScore = CategoryScore::where('category_scores.survey_id', $currentSurveyId)
                ->where('category_scores.department_id', $selectedDeptId)
                ->where('category_scores.category_id', $previousBottleneck['category_id'])
                ->selectRaw('CASE WHEN expectation_gap > 0 THEN expectation_gap * avg_expectation ELSE 0 END as score')
                ->value('score');
        }

        $historyData = $this->analyticsService->getHistoricalData($currentBottleneck['category_id'], $selectedDeptId);

        $latestData = $historyData->last();
        $previousData = $historyData->slice(-2, 1)->first();

        $latestExpectation = $latestData?->avg_expectation ?? null;
        $previousExpectation = $previousData?->avg_expectation ?? null;
        $latestSatisfaction = $latestData?->avg_satisfaction ?? null;
        $previousSatisfaction = $previousData?->avg_satisfaction ?? null;

        $expectationDifference = ($latestExpectation !== null && $previousExpectation !== null)
            ? $latestExpectation - $previousExpectation
            : null;

        $satisfactionDifference = ($latestSatisfaction !== null && $previousSatisfaction !== null)
            ? $latestSatisfaction - $previousSatisfaction
            : null;

        $subcategoryScoreHistory = $currentBottleneck
            ? $this->analyticsService->getSubcategoryScoreHistory($selectedDeptId, $currentBottleneck['category_id'])
            : collect();

        $latestSubcategoryData = $subcategoryScoreHistory->groupBy('subcategory_id')->map(function ($items) {
            return $items->last();
        });

        // サブ項目のデータを取得
        $subCategoryScores = SubcategoryScore::where('survey_id', $currentSurveyId)
            ->where('department_id', $selectedDeptId)
            ->join('subcategories', 'subcategory_scores.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->get([
                'categories.id as category_id',
                'categories.name as category_name',
                'subcategories.id',
                'subcategories.name as question',
                'subcategories.question as description',
                'subcategory_scores.avg_score as avg_satisfaction'
            ]);

        $title = 'Analytics';

        return view('analytics', compact(
            'currentDeptId',
            'departments',
            'title',
            'currentBottleneck',
            'previousBottleneck',
            'previousBottleneckCurrentScore',
            'latestExpectation',
            'latestSatisfaction',
            'expectationDifference',
            'satisfactionDifference',
            'subcategoryScoreHistory',
            'latestSubcategoryData',
            'historyData',
            'subCategoryScores',
            'currentYear',
            'currentPeriod',
            'availablePeriods'
        ));
    }

    public function analyticsDetail(Request $request, SurveyService $surveyService)
    {
        $user = Auth::user();
        $selectedDeptId = $request->get('dept_id', $user->department_id);
        $currentYear = $request->get('year', date('Y'));
        $currentPeriod = $request->get('period', 'first');
        
        // 利用可能な期間を取得
        $availablePeriods = $surveyService->getAvailablePeriods($currentYear, $selectedDeptId);
        
        // 利用可能な期間がない場合は、デフォルトで上半期を指定
        if (!$currentPeriod || empty($availablePeriods) || !collect($availablePeriods)->pluck('value')->contains($currentPeriod)) {
            $currentPeriod = 'first';
        }

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($selectedDeptId, $currentYear, $currentPeriod);
        $currentSurveyId = $currentSurvey?->id;
        $previousSurveyId = $surveyService->previousSurveyId($currentSurveyId, $selectedDeptId);

        // 主要項目のデータを取得
        $categoryScores = CategoryScore::where('survey_id', $currentSurveyId)
            ->where('department_id', $selectedDeptId)
            ->join('categories', 'category_scores.category_id', '=', 'categories.id')
            ->get(['categories.id', 'categories.name', 'category_scores.avg_expectation', 'category_scores.avg_satisfaction']);

        // 前回のスコアを取得
        $previousScores = CategoryScore::where('survey_id', $previousSurveyId)
            ->where('department_id', $selectedDeptId)
            ->join('categories', 'category_scores.category_id', '=', 'categories.id')
            ->get(['categories.id', 'category_scores.avg_expectation', 'category_scores.avg_satisfaction']);

        // サブ項目のデータを取得
        $subCategoryScores = SubcategoryScore::where('survey_id', $currentSurveyId)
            ->where('department_id', $selectedDeptId)
            ->join('subcategories', 'subcategory_scores.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->get([
                'categories.id as category_id',
                'categories.name as category_name',
                'subcategories.id',
                'subcategories.name as question',
                'subcategories.question as description',
                'subcategory_scores.avg_score as avg_satisfaction'
            ]);

        // サブ項目の前回のスコアを取得
        $previousSubScores = SubcategoryScore::where('survey_id', $previousSurveyId)
            ->where('department_id', $selectedDeptId)
            ->join('subcategories', 'subcategory_scores.subcategory_id', '=', 'subcategories.id')
            ->get(['subcategories.id', 'subcategory_scores.avg_score as avg_satisfaction']);

        // メインデータを整形
        $analyticsData = $categoryScores->map(function ($score) use ($previousScores) {
            $previousScore = $previousScores->firstWhere('id', $score->id);
            
            return [
                'id' => $score->id,
                'name' => $score->name,
                'current_expectation' => round($score->avg_expectation, 2),
                'current_satisfaction' => round($score->avg_satisfaction, 2),
                'previous_expectation' => $previousScore ? round($previousScore->avg_expectation, 2) : null,
                'previous_satisfaction' => $previousScore ? round($previousScore->avg_satisfaction, 2) : null,
                'expectation_diff' => $previousScore ? round($score->avg_expectation - $previousScore->avg_expectation, 2) : null,
                'satisfaction_diff' => $previousScore ? round($score->avg_satisfaction - $previousScore->avg_satisfaction, 2) : null,
            ];
        });

        // サブ項目データを整形
        $subAnalyticsData = $subCategoryScores->groupBy('category_id')->map(function ($items) use ($previousSubScores) {
            return $items->map(function ($item) use ($previousSubScores) {
                $previousScore = $previousSubScores->firstWhere('id', $item->id);
                $satisfactionDiff = $previousScore ? round($item->avg_satisfaction - $previousScore->avg_satisfaction, 2) : null;

                return [
                    'category_id' => $item->category_id,
                    'category_name' => $item->category_name,
                    'name' => $item->question,
                    'description' => $item->description,
                    'current_satisfaction' => round($item->avg_satisfaction, 2),
                    'satisfaction_diff' => $satisfactionDiff
                ];
            });
        });

        $departments = Department::orderBy('id')->get();
        $currentDeptId = $selectedDeptId;

        return view('analytics_detail', compact(
            'analyticsData', 
            'subAnalyticsData', 
            'departments', 
            'currentDeptId',
            'currentYear',
            'currentPeriod',
            'availablePeriods'
        ));
    }
}
