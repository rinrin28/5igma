<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\ExecutiveAnalyticsService;
use App\Services\ExecutiveSurveyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\SubcategoryScore;
use App\Models\CategoryScore;

class ExecutiveAnalyticsController extends Controller
{
    /**
     * 経営層用の分析データを表示
     */
    public function index(Request $request, ExecutiveAnalyticsService $analyticsService, ExecutiveSurveyService $surveyService)
    {
        $user = Auth::user();
        $currentYear = $request->get('year', date('Y'));
        
        // 利用可能な期間を取得
        $availablePeriods = $surveyService->getAvailablePeriods($currentYear);
        
        // 利用可能な期間がない場合は、デフォルトで上半期を指定
        $currentPeriod = $request->get('period', 'first');
        if (!$currentPeriod || empty($availablePeriods) || !collect($availablePeriods)->pluck('value')->contains($currentPeriod)) {
            $currentPeriod = 'first';
        }

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        
        // 2025年の下半期のデータがない場合は、上半期のデータを表示
        if ($currentYear == 2025 && $currentPeriod == 'second' && !$currentSurvey) {
            $currentPeriod = 'first';
            $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        }
        
        $currentSurveyId = $currentSurvey?->id;

        // 分析データを取得
        $analyticsData = $analyticsService->getAnalytics($currentSurveyId);

        // 部署一覧を取得
        $departments = Department::orderBy('id')->get();

        // ボトルネック情報を取得
        $bottleneck = $analyticsService->getBottleneck($currentSurveyId);
        $bottleneckTitle = $bottleneck['title'] ?? null;
        $bottleneckScore = $bottleneck['score'] ?? null;

        // AI推奨事項を取得
        $aiRecommendations = $analyticsService->getAIRecommendations($currentSurveyId);

        // 回答状況を取得
        $validCount = $surveyService->countSubmitted($currentSurveyId);
        $totalCount = $surveyService->countUsers($currentSurveyId);
        $responseRate = $surveyService->calculateResponseRate($currentSurveyId);
        $deltaRate = $surveyService->calculateDeltaRate($currentSurveyId);

        // 満足度データを取得
        $previousData = $analyticsService->getPreviousSatisfactionData($currentSurveyId);
        $currentData = $analyticsService->getCurrentSatisfactionData($currentSurveyId);
        $latestScore = $analyticsService->getLatestSatisfactionScore($currentSurveyId);
        $scoreDifference = $analyticsService->getSatisfactionScoreDifference($currentSurveyId);
        $satisfactionData = $analyticsService->getSatisfactionData($currentSurveyId);

        // ボトルネックスコアデータを取得
        $bottleneckDataResult = $analyticsService->getBottleneckData($currentSurveyId);
        $latestBottleneckScore = $bottleneckDataResult['latestBottleneckScore'];
        $bottleneckDifference = $bottleneckDataResult['bottleneckDifference'];
        $bottleneckData = json_decode(json_encode($bottleneckDataResult['bottleneckData']), true);

        // マトリクスデータを取得
        $matrixData = $analyticsService->getMatrixData($currentSurveyId);

        return view('executive.analytics', compact(
            'currentSurvey',
            'analyticsData',
            'departments',
            'currentYear',
            'currentPeriod',
            'availablePeriods',
            'bottleneck',
            'bottleneckTitle',
            'bottleneckScore',
            'aiRecommendations',
            'validCount',
            'totalCount',
            'responseRate',
            'deltaRate',
            'previousData',
            'currentData',
            'latestScore',
            'scoreDifference',
            'satisfactionData',
            'latestBottleneckScore',
            'bottleneckDifference',
            'bottleneckData',
            'matrixData'
        ));
    }

    /**
     * 経営層用の分析データをJSON形式で取得
     */
    public function getData(Request $request, ExecutiveAnalyticsService $analyticsService, ExecutiveSurveyService $surveyService)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentPeriod = $request->get('period', 'first');

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        
        // 2025年の下半期のデータがない場合は、上半期のデータを表示
        if ($currentYear == 2025 && $currentPeriod == 'second' && !$currentSurvey) {
            $currentPeriod = 'first';
            $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        }
        
        $currentSurveyId = $currentSurvey?->id;

        // 分析データを取得
        $analyticsData = $analyticsService->getAnalytics($currentSurveyId);

        return response()->json($analyticsData);
    }

    /**
     * 経営層用の詳細分析データを表示
     */
    public function analyticsDetail(Request $request, ExecutiveAnalyticsService $analyticsService, ExecutiveSurveyService $surveyService)
    {
        $user = Auth::user();
        $currentYear = $request->get('year', date('Y'));
        
        // 利用可能な期間を取得
        $availablePeriods = $surveyService->getAvailablePeriods($currentYear);
        
        // 利用可能な期間がない場合は、デフォルトで上半期を指定
        $currentPeriod = $request->get('period', 'first');
        if (!$currentPeriod || empty($availablePeriods) || !collect($availablePeriods)->pluck('value')->contains($currentPeriod)) {
            $currentPeriod = 'first';
        }

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        
        // 2025年の下半期のデータがない場合は、上半期のデータを表示
        if ($currentYear == 2025 && $currentPeriod == 'second' && !$currentSurvey) {
            $currentPeriod = 'first';
            $currentSurvey = $surveyService->getByYearAndPeriod($currentYear, $currentPeriod);
        }
        
        $currentSurveyId = $currentSurvey?->id;

        // ボトルネック情報を取得
        $bottleneck = $analyticsService->getBottleneck($currentSurveyId);
        $bottleneckTitle = $bottleneck['title'] ?? null;
        $bottleneckScore = $bottleneck['score'] ?? null;

        // AI推奨事項を取得
        $aiRecommendations = $analyticsService->getAIRecommendations($currentSurveyId);

        // 回答状況を取得
        $validCount = $surveyService->countSubmitted($currentSurveyId);
        $totalCount = $surveyService->countUsers($currentSurveyId);
        $responseRate = $surveyService->calculateResponseRate($currentSurveyId);
        $deltaRate = $surveyService->calculateDeltaRate($currentSurveyId);

        // 満足度データを取得
        $previousData = $analyticsService->getPreviousSatisfactionData($currentSurveyId);
        $currentData = $analyticsService->getCurrentSatisfactionData($currentSurveyId);
        $latestScore = $analyticsService->getLatestSatisfactionScore($currentSurveyId);
        $scoreDifference = $analyticsService->getSatisfactionScoreDifference($currentSurveyId);
        $satisfactionData = $analyticsService->getSatisfactionData($currentSurveyId);

        // ボトルネックスコアデータを取得
        $bottleneckDataResult = $analyticsService->getBottleneckData($currentSurveyId);
        $latestBottleneckScore = $bottleneckDataResult['latestBottleneckScore'];
        $bottleneckDifference = $bottleneckDataResult['bottleneckDifference'];
        $bottleneckData = $bottleneckDataResult['bottleneckData'];

        // マトリクスデータを取得
        $matrixData = $analyticsService->getMatrixData($currentSurveyId);

        // 前回のボトルネックを取得
        $previousBottleneck = null;
        $previousBottleneckCurrentScore = null;
        if ($currentSurvey) {
            $previousSurvey = Survey::where('survey_types_id', 1)
                ->where('start_date', '<', $currentSurvey->start_date)
                ->orderBy('start_date', 'desc')
                ->first();
            
            if ($previousSurvey) {
                $previousBottleneck = $analyticsService->getBottleneck($previousSurvey->id);
                $previousBottleneckCurrentScore = $analyticsService->getPreviousBottleneckCurrentScore($currentSurveyId);
            }
        }

        // 履歴データを取得
        $historyData = $analyticsService->getHistoricalData($currentSurveyId);

        // サブカテゴリスコアの履歴を取得
        $subcategoryScoreHistory = $analyticsService->getSubcategoryScoreHistory($currentSurveyId);

        // 最新のサブカテゴリデータを取得
        $latestSubcategoryData = collect();
        if ($currentSurvey) {
            $latestSubcategoryData = SubcategoryScore::where('survey_id', $currentSurveyId)
                ->with(['subcategory.category', 'department'])
                ->get();
        }

        // 期待度と満足度の差分を計算
        $latestExpectation = 0;
        $latestSatisfaction = 0;
        $expectationDifference = 0;
        $satisfactionDifference = 0;

        if ($currentSurvey) {
            $scores = CategoryScore::where('survey_id', $currentSurveyId)->get();
            if ($scores->isNotEmpty()) {
                $latestExpectation = $scores->avg('avg_expectation');
                $latestSatisfaction = $scores->avg('avg_satisfaction');
            }

            $previousSurvey = Survey::where('survey_types_id', 1)
                ->where('start_date', '<', $currentSurvey->start_date)
                ->orderBy('start_date', 'desc')
                ->first();
            
            if ($previousSurvey) {
                $previousScores = CategoryScore::where('survey_id', $previousSurvey->id)->get();
                if ($previousScores->isNotEmpty()) {
                    $previousExpectation = $previousScores->avg('avg_expectation');
                    $previousSatisfaction = $previousScores->avg('avg_satisfaction');
                    
                    $expectationDifference = $latestExpectation - $previousExpectation;
                    $satisfactionDifference = $latestSatisfaction - $previousSatisfaction;
                }
            }
        }

        return view('executive.analytics_detail', compact(
            'currentSurvey',
            'currentYear',
            'currentPeriod',
            'availablePeriods',
            'bottleneck',
            'bottleneckTitle',
            'bottleneckScore',
            'aiRecommendations',
            'validCount',
            'totalCount',
            'responseRate',
            'deltaRate',
            'previousData',
            'currentData',
            'latestScore',
            'scoreDifference',
            'satisfactionData',
            'latestBottleneckScore',
            'bottleneckDifference',
            'bottleneckData',
            'matrixData',
            'previousBottleneck',
            'previousBottleneckCurrentScore',
            'historyData',
            'subcategoryScoreHistory',
            'latestSubcategoryData',
            'latestExpectation',
            'latestSatisfaction',
            'expectationDifference',
            'satisfactionDifference'
        ));
    }
} 