<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\User;
use App\Models\Expectation;
use App\Models\Satisfaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SurveyService
{
    /**
     * 指定部署の最新 Survey を取得する
     *
     * @param  int|null  $departmentId  部署ID (null の場合 null を返す)
     */
    public function latestForDepartment(?int $departmentId): ?Survey
    {
        $query = Survey::where('department_id', $departmentId)
        ->where('survey_types_id', 1)
        ->where('start_date', '<=', now())
        ->latest('start_date');


return $query->first();
    }

    /**
     * 最新アンケートにおける主要ボトルネック項目とスコアを取得
     * 計算式: avg_expectation × (avg_expectation - avg_satisfaction)
     *
     * @param  int  $surveyId  アンケートID
     * @param  int  $departmentId  部署ID
     * @return array|null ['category_id'=>int, 'title'=>string, 'score'=>float] or null
     */
    public function getLatestBottleneck(int $surveyId, int $departmentId): ?array
    {
        $record = DB::table('category_scores')
            ->join('categories', 'category_scores.category_id', '=', 'categories.id')
            ->selectRaw('category_scores.category_id, categories.question AS title, avg_expectation * (avg_expectation - avg_satisfaction) AS score')
            ->where('survey_id', $surveyId)
            ->where('department_id', $departmentId)
            ->whereRaw('avg_expectation * (avg_expectation - avg_satisfaction) > 0')
            ->orderByDesc('score')
            ->first();

        return $record
            ? ['category_id' => $record->category_id, 'title' => $record->title, 'score' => round($record->score, 2)]
            : null;
    }

    /**
     * 指定カテゴリのサブ項目平均スコアを取得
     *
     * @param  int  $surveyId  アンケートID
     * @param  int  $categoryId  カテゴリID
     * @return array [{name=>string, avg_score=>float}, ...]
     */
    public function getSubAverages(int $surveyId, int $categoryId): array
    {
        return DB::table('sub_responses')
            ->join('subcategories', 'sub_responses.subcategory_id', '=', 'subcategories.id')
            ->select('subcategories.name', DB::raw('AVG(score) AS avg_score'))
            ->where('survey_id', $surveyId)
            ->where('subcategories.category_id', $categoryId)
            ->groupBy('subcategories.name')
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'avg_score' => round($r->avg_score, 2)])
            ->toArray();
    }

    /**
     * 指定部署の最新満足度平均(%)を取得
     *
     * @param  int  $surveyId  アンケートID
     * @param  int  $departmentId  部署ID
     * @return float|null 満足度平均（0-100） or null
     */
    public function getLatestSatisfactionScore(int $surveyId, int $departmentId): ?float
    {
        $avg = DB::table('category_scores')
            ->where('survey_id', $surveyId)
            ->where('department_id', $departmentId)
            ->avg('avg_satisfaction');

        return $avg !== null ? round($avg * 20, 2) : null;
    }

    public function previousSurveyId(?int $currentSurveyId, int $departmentId): ?int
    {
        if (! $currentSurveyId) {
            return null;
        }

        $currentSurvey = Survey::find($currentSurveyId);
        if (!$currentSurvey) {
            return null;
        }

        return Survey::where('department_id', $departmentId)
            ->where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->latest('start_date')
            ->value('id');
    }

    /**
     * 提出済みユーザー数を取得
     */
    public function countSubmitted(int $surveyId, int $departmentId): int
    {
        if (!$surveyId) {
            return 0;
        }

        return Expectation::where('survey_id', $surveyId)
            ->whereHas('user', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('is_submitted', true)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * 部署内ユーザー数を取得
     */
    public function countDepartmentUsers(int $departmentId): int
    {
        return User::where('department_id', $departmentId)->count();
    }

    /**
     * 回答率 (%) を計算
     */
    public function calculateResponseRate(int $surveyId, int $departmentId): float
    {
        $totalUsers = $this->countDepartmentUsers($departmentId);
        if ($totalUsers === 0) {
            return 0;
        }

        $submittedUsers = $this->countSubmitted($surveyId, $departmentId);
        return ($submittedUsers / $totalUsers) * 100;
    }

    /**
     * 回答率の前回比 (%) を計算
     */
    public function calculateDeltaRate(int $surveyId, int $departmentId): float
    {
        if (!$surveyId) {
            return 0.0;
        }

        $currentRate = $this->calculateResponseRate($surveyId, $departmentId);
        $previousSurveyId = $this->previousSurveyId($surveyId, $departmentId);

        if (!$previousSurveyId) {
            return 0.0;
        }

        $previousRate = $this->calculateResponseRate($previousSurveyId, $departmentId);
        return $currentRate - $previousRate;
    }

    public function getByYearAndPeriod($departmentId, $year, $period)
    {
        return Survey::where('department_id', $departmentId)
            ->where('survey_types_id', 1)
            ->whereYear('start_date', $year)
            ->when($period === 'first', function ($query) use ($year) {
                return $query->whereBetween('start_date', [
                    Carbon::createFromDate($year, 4, 1)->startOfDay(),
                    Carbon::createFromDate($year, 9, 30)->endOfDay()
                ]);
            })
            ->when($period === 'second', function ($query) use ($year) {
                return $query->whereBetween('start_date', [
                    Carbon::createFromDate($year, 10, 1)->startOfDay(),
                    Carbon::createFromDate($year, 12, 31)->endOfDay()
                ]);
            })
            ->first();
    }

    public function getAvailablePeriods($year, $departmentId)
    {
        $periods = [];
        
        // 上半期（4月〜9月）のチェック
        $firstHalfQuery = Survey::where('department_id', $departmentId)
            ->where('survey_types_id', 1)
            ->whereYear('start_date', $year)
            ->whereBetween('start_date', [
                Carbon::createFromDate($year, 4, 1)->startOfDay(),
                Carbon::createFromDate($year, 9, 30)->endOfDay()
            ]);
            
        $hasFirstHalf = $firstHalfQuery->exists();
            
        // 下半期（10月〜12月）のチェック
        $secondHalfQuery = Survey::where('department_id', $departmentId)
            ->where('survey_types_id', 1)
            ->whereYear('start_date', $year)
            ->whereBetween('start_date', [
                Carbon::createFromDate($year, 10, 1)->startOfDay(),
                Carbon::createFromDate($year, 12, 31)->endOfDay()
            ]);
            
        $hasSecondHalf = $secondHalfQuery->exists();
        
        // 2025年の下半期のデータがない場合は、下半期を選択肢に含めない
        if ($year == 2025) {
            $hasSecondHalf = false;
        }
            
        if ($hasFirstHalf) {
            $periods[] = [
                'value' => 'first',
                'label' => '上半期（4月〜9月）'
            ];
        }
        
        if ($hasSecondHalf) {
            $periods[] = [
                'value' => 'second',
                'label' => '下半期（10月〜12月）'
            ];
        }
        
        return $periods;
    }
}
