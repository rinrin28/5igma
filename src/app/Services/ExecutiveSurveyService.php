<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\Satisfaction;
use App\Models\Expectation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveSurveyService
{
    /**
     * 経営層用の利用可能な期間を取得
     */
    public function getAvailablePeriods($year): array
    {
        $periods = [];
        
        // 上半期（4月〜9月）のチェック
        $firstHalfQuery = Survey::where('survey_types_id', 1)
            ->whereYear('start_date', $year)
            ->whereBetween('start_date', [
                Carbon::createFromDate($year, 4, 1)->startOfDay(),
                Carbon::createFromDate($year, 9, 30)->endOfDay()
            ]);
            
        $hasFirstHalf = $firstHalfQuery->exists();
            
        // 下半期（10月〜12月）のチェック
        $secondHalfQuery = Survey::where('survey_types_id', 1)
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

    /**
     * 経営層用の年度と期間による調査データ取得
     */
    public function getByYearAndPeriod($year, $period): ?Survey
    {
        return Survey::where('survey_types_id', 1)
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

    /**
     * 経営層用の前回の調査IDを取得
     */
    public function previousSurveyId(?int $currentSurveyId): ?int
    {
        if (!$currentSurveyId) {
            return null;
        }

        $currentSurvey = Survey::find($currentSurveyId);
        if (!$currentSurvey) {
            return null;
        }

        return Survey::where('survey_types_id', 1)
            ->where('start_date', '<', $currentSurvey->start_date)
            ->orderBy('start_date', 'desc')
            ->value('id');
    }

    /**
     * 経営層用のユーザー数を取得
     */
    public function countUsers(): int
    {
        // 経営層は department_id が null の可能性があるため、すべてのユーザーをカウント
        return DB::table('users')
            ->whereIn('role', ['employee', 'management', 'executive', 'admin'])
            ->count();
    }

    /**
     * 経営層用の回答数を取得
     */
    private function countResponses(int $surveyId): int
    {
        // satisfactions テーブルから回答数を取得
        $satisfactionCount = DB::table('satisfactions')
            ->where('survey_id', $surveyId)
            ->where('is_submitted', true)
            ->distinct('user_id')
            ->count();

        // expectations テーブルから回答数を取得
        $expectationCount = DB::table('expectations')
            ->where('survey_id', $surveyId)
            ->where('is_submitted', true)
            ->distinct('user_id')
            ->count();

        // 両方のテーブルで回答しているユーザーの数を返す
        return max($satisfactionCount, $expectationCount);
    }

    /**
     * 経営層用の回答率を計算
     */
    public function calculateResponseRate(int $surveyId): float
    {
        // 全ユーザー数を取得（部署に関係なく）
        $totalCount = $this->countUsers();
        
        // 提出済み回答数を取得
        $validCount = $this->countResponses($surveyId);
        
        // 回答率を計算（パーセント表示）
        if ($totalCount === 0) {
            return 0;
        }
        
        return ($validCount / $totalCount) * 100;
    }

    /**
     * 経営層用の前回比を計算
     */
    public function calculateDeltaRate(int $surveyId): float
    {
        // 現在の回答率を取得
        $currentRate = $this->calculateResponseRate($surveyId);
        
        // 前回の調査IDを取得
        $previousSurveyId = $this->previousSurveyId($surveyId);
        if (!$previousSurveyId) {
            return 0;
        }
        
        // 前回の回答率を取得
        $previousRate = $this->calculateResponseRate($previousSurveyId);
        
        // 前回比を計算（パーセント表示）
        if ($previousRate === 0) {
            return 0;
        }
        
        return (($currentRate - $previousRate) / $previousRate) * 100;
    }

    /**
     * 経営層用の提出済み回答数を取得
     */
    public function countSubmitted(int $surveyId): int
    {
        // countResponses メソッドを使用して回答数を取得
        return $this->countResponses($surveyId);
    }
} 