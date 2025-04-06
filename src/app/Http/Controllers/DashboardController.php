<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\ChartService;
use App\Services\SurveyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, SurveyService $surveyService, ChartService $chartService)
    {
        $user = Auth::user();
        $currentDeptId = $request->get('dept_id', $user->department_id);
        $currentYear = $request->get('year', date('Y'));
        
        // 利用可能な期間を取得
        $availablePeriods = $surveyService->getAvailablePeriods($currentYear, $currentDeptId);
        
        // 利用可能な期間がない場合は、デフォルトで上半期を指定
        $currentPeriod = $request->get('period', 'first');
        if (!$currentPeriod || empty($availablePeriods) || !collect($availablePeriods)->pluck('value')->contains($currentPeriod)) {
            $currentPeriod = 'first';
        }

        // 年度と期間を指定してデータを取得
        $currentSurvey = $surveyService->getByYearAndPeriod($currentDeptId, $currentYear, $currentPeriod);
        
        // 2025年の下半期のデータがない場合は、上半期のデータを表示
        if ($currentYear == 2025 && $currentPeriod == 'second' && !$currentSurvey) {
            $currentPeriod = 'first';
            $currentSurvey = $surveyService->getByYearAndPeriod($currentDeptId, $currentYear, $currentPeriod);
        }
        
        $currentSurveyId = $currentSurvey?->id;
        $previousSurveyId = $surveyService->previousSurveyId($currentSurveyId, $currentDeptId);

        $currentData = $chartService->getCurrentData($currentSurveyId, $currentDeptId);
        $previousData = $chartService->getPreviousData($previousSurveyId, $currentDeptId);
        $matrixData = $chartService->getMatrixData($currentSurveyId, $currentDeptId);

        $totalCount = $surveyService->countDepartmentUsers($currentDeptId);
        $validCount = $surveyService->countSubmitted($currentSurveyId, $currentDeptId);
        $responseRate = $surveyService->calculateResponseRate($currentSurveyId, $currentDeptId);
        $deltaRate = $surveyService->calculateDeltaRate($currentSurveyId, $currentDeptId);

        $departments = Department::orderBy('id')->get();

        $bottleneck = collect($matrixData)->filter(fn ($item) => ! is_null($item['score']))
            ->sortByDesc('score')
            ->first();

        $satisfactionData = $chartService->getSatisfactionData($currentDeptId);

        $currentSatisfactionValues = array_values($currentData);
        $previousSatisfactionValues = array_values($previousData);
        $latestScore = ! empty($currentSatisfactionValues) ? (array_sum($currentSatisfactionValues) / count($currentSatisfactionValues) / 5) * 100 : null;
        $previousScore = ! empty($previousSatisfactionValues) ? (array_sum($previousSatisfactionValues) / count($previousSatisfactionValues) / 5) * 100 : null;
        $scoreDifference = ($latestScore !== null && $previousScore !== null) ? $latestScore - $previousScore : null;

        $bottleneckResult = $chartService->getBottleneckData($currentDeptId, $currentSurveyId, $previousSurveyId);
        $bottleneckData = $bottleneckResult['bottleneckData'];
        $latestBottleneckScore = $bottleneckResult['latestBottleneckScore'];
        $bottleneckDifference = $bottleneckResult['bottleneckDifference'];

        return view('dashboard', compact(
            'currentSurvey', 'totalCount', 'validCount', 'responseRate', 'deltaRate',
            'currentData', 'previousData', 'matrixData', 'departments', 'currentDeptId',
            'bottleneck', 'satisfactionData', 'latestScore',
            'scoreDifference', 'bottleneckData', 'latestBottleneckScore', 'bottleneckDifference',
            'currentYear', 'currentPeriod', 'availablePeriods'
        ));
    }
}
