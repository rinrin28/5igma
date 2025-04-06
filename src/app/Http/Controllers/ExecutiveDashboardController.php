<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\ExecutiveChartService;
use App\Services\ExecutiveSurveyService;
use App\Services\ExecutiveAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    /**
     * 経営層用ダッシュボードを表示
     */
    public function index(Request $request, ExecutiveSurveyService $surveyService, ExecutiveChartService $chartService, ExecutiveAnalyticsService $analyticsService)
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
        $previousSurveyId = $surveyService->previousSurveyId($currentSurveyId);

        // 全社データを取得
        $currentData = $chartService->getCurrentData($currentSurveyId);
        $previousData = $chartService->getPreviousData($previousSurveyId);
        $matrixData = $chartService->getMatrixData($currentSurveyId);
        $bottleneckDataResult = $chartService->getBottleneckData($currentSurveyId, $previousSurveyId);
        $latestBottleneckScore = $bottleneckDataResult['latestBottleneckScore'];
        $bottleneckDifference = $bottleneckDataResult['bottleneckDifference'];
        $bottleneckData = json_decode(json_encode($bottleneckDataResult['bottleneckData']), true);
        
        // 回答数と回答率を取得
        $totalCount = $surveyService->countUsers();
        $validCount = $surveyService->countSubmitted($currentSurveyId);
        $responseRate = $surveyService->calculateResponseRate($currentSurveyId);
        $deltaRate = $surveyService->calculateDeltaRate($currentSurveyId);

        // 部署情報を取得（経営層向けに全社データとして扱う）
        $departments = Department::orderBy('id')->get();

        // ボトルネック情報を取得
        $bottleneck = collect($matrixData)->filter(fn ($item) => ! is_null($item['score']))
            ->sortByDesc('score')
            ->first();

        // 満足度データを取得
        $satisfactionData = $chartService->getSatisfactionData();

        // 満足度スコアを計算
        $currentSatisfactionValues = array_values($currentData);
        $previousSatisfactionValues = array_values($previousData);
        $latestScore = ! empty($currentSatisfactionValues) ? (array_sum($currentSatisfactionValues) / count($currentSatisfactionValues) / 5) * 100 : null;
        $previousScore = ! empty($previousSatisfactionValues) ? (array_sum($previousSatisfactionValues) / count($previousSatisfactionValues) / 5) * 100 : null;
        $scoreDifference = ($latestScore !== null && $previousScore !== null) ? $latestScore - $previousScore : null;

        // 部署別スコアを取得（経営層向けに全社データとして扱う）
        $departmentScores = $analyticsService->getAnalytics($currentSurveyId)['department_scores'] ?? [];

        return view('executive.dashboard', compact(
            'currentSurvey', 'totalCount', 'validCount', 'responseRate', 'deltaRate',
            'currentData', 'previousData', 'matrixData', 'departments',
            'bottleneck', 'satisfactionData', 'latestScore',
            'scoreDifference', 'bottleneckData', 'latestBottleneckScore', 'bottleneckDifference',
            'currentYear', 'currentPeriod', 'availablePeriods', 'departmentScores'
        ));
    }
} 