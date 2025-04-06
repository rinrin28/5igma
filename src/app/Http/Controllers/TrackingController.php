<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

use App\Services\SurveyService;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Proposal;
use App\Models\PulseSurvey;
use App\Models\Task;
use App\Models\Survey;
use App\Models\ShortTermGoal;
use App\Models\FeedbackAnswer;
use App\Models\FeedbackQuestion;
use App\Models\User;

class TrackingController extends Controller
{
    public function index(Request $request, SurveyService $surveyService, AnalyticsService $analyticsService)
    {

        $user = Auth::user();

        $selectedDeptId = (int)$request->query('dept_id', $user->department_id);
    
        // 選択部署の最新 Survey モデルを取得
        $currentSurvey = $surveyService->latestForDepartment($selectedDeptId);
        $surveyId = $currentSurvey?->id; // 最新SurveyのID (null許容)
        // ボトルネック項目 (タイトル＋スコア) を取得
        $bottleneck        = $surveyService->getLatestBottleneck($surveyId, $selectedDeptId);
        $bottleneckTitle = $bottleneck['title'];
        $bottleneckScore = $bottleneck['score'];

        // セッションから読み出し（なければ空配列／文字列）
        $feedback = session('planningFeedback', []);
        $aiJson = session('aiRecommendations', '');
        $aiData = json_decode($aiJson, true) ?: [];

        $raw = $aiData['recommendations'] ?? $aiData;

        $recommendations = collect($raw)
            ->map(fn($item, $key) => [
                'id'          => $key,
                'title'       => $key,
                'description' => is_array($item)
                    ? implode("\n", array_values($item))
                    : $item,
            ])
            ->values()
            ->toArray();

        // 全部署リスト (選択切替用)
        $departments = Department::orderBy('id')->get();
        $currentDeptId = $selectedDeptId; // Bladeで使用する選択中部署ID

        $proposal = Proposal::where('department_id', $selectedDeptId)
                        ->latest()
                        ->first();

        $pulseSurvey = $proposal ? PulseSurvey::where('proposal_id', $proposal->id)->first() : null;
        $survey = $pulseSurvey ? Survey::find($pulseSurvey->survey_id) : null;
        $milestones = $proposal ? Task::where('proposal_id', $proposal->id)->select('id', 'proposal_id','name', 'date', 'status')->get() : collect();

        $currentBottleneck = $analyticsService->getBottleneck($currentSurvey->id, $selectedDeptId);
        $historyData = $analyticsService->getHistoricalData($currentBottleneck['category_id'], $selectedDeptId);
        $latestData = $historyData->last();
        $latestSatisfaction = $latestData?->avg_satisfaction ?? null;

        $latestProposal = Proposal::where('department_id', $selectedDeptId)
                    ->orderBy('created_at', 'desc')
                    ->first();

        $proposalId = $latestProposal ? $latestProposal->id : null;

        $pastProposal=Proposal::where('survey_id', $surveyId);

        $tasks = DB::table('tasks')
        ->where('proposal_id', $proposalId)
        ->select('id', 'name', 'date', DB::raw("'task' as type"))
        ->get();

        $pulseSurveys = DB::table('pulse_surveys as ps')
        ->join('surveys as s', 'ps.survey_id', '=', 's.id')
        ->where('ps.proposal_id', $proposalId)
        ->select('ps.id as pulse_id', 's.start_date', 's.end_date')
        ->get();

    $pulseEvents = collect();
    foreach ($pulseSurveys as $survey) {
        $pulseEvents->push((object)[
            'id' => $survey->pulse_id,
            'date' => $survey->start_date,
            'type' => 'pulse_start',
        ]);
        $pulseEvents->push((object)[
            'id' => $survey->pulse_id,
            'date' => $survey->end_date,
            'type' => 'pulse_end',
        ]);
    }

    // マージして時系列順に並べる
    $items = $tasks->merge($pulseEvents)->sortBy('date')->values();


        $tasks = DB::table('tasks')
        ->where('proposal_id', $proposalId)
        ->select('id', 'name', 'date', DB::raw("'task' as type"))
        ->get();

    $pulseSurveys = DB::table('pulse_surveys as ps')
        ->join('surveys as s', 'ps.survey_id', '=', 's.id')
        ->where('ps.proposal_id', $proposalId)
        ->select('ps.id as pulse_id', 's.start_date', 's.end_date')
        ->get();

    $pulseEvents = collect();
    foreach ($pulseSurveys as $survey) {
        $pulseEvents->push((object)[
            'id' => $survey->pulse_id,
            'date' => $survey->start_date,
            'type' => 'pulse_start',
        ]);
        $pulseEvents->push((object)[
            'id' => $survey->pulse_id,
            'date' => $survey->end_date,
            'type' => 'pulse_end',
        ]);
    }

    // マージして時系列順に並べる
    $items = $tasks->merge($pulseEvents)->sortBy('date')->values();

        $pulseSurveyId = PulseSurvey::where('proposal_id', $proposalId)
        ->value('survey_id');

        $survey = Survey::find($pulseSurveyId);

        if (!$survey) {
            return abort(404, 'Survey not found');
        }

        // department_id に対応するユーザー数をカウント
        $userCount = User::where('department_id', $survey->department_id)->count();

        // feedback_answers テーブルで is_completed=1 の user_id をカウント
        $answeredUsersCount = FeedbackAnswer::where('survey_id', $survey->id)
            ->where('department_id', $survey->department_id)
            ->where('is_completed', true)
            ->select('user_id') // user_id のみを選択
            ->distinct() // 重複を排除
            ->count();

        // 回答済みユーザー数を9で割る
        $answeredUsersCount = $answeredUsersCount / 9;

        // 回答率を計算（有効数字3桁のパーセンテージ）
        $responseRate = $userCount > 0 ? round(($answeredUsersCount / $userCount) * 100, 1) : 0;

        // 質問データを取得
        $questions = FeedbackQuestion::all();

        // 各質問に対する回答の割合を計算
        $questionAnswers = [];
        foreach ($questions as $question) {
            $answers = FeedbackAnswer::where('survey_id', $survey->id)
                ->where('feedback_question_id', $question->id)
                ->where('department_id', $survey->department_id)
                ->pluck('answer');

            $totalAnswers = $answers->count();
            $answerCounts = $answers->countBy();

            // 各回答（1〜5）の割合を計算
            $questionAnswers[$question->id] = [
                '1' => $totalAnswers > 0 ? round(($answerCounts->get(1, 0) / $totalAnswers) * 100, 1) : 0,
                '2' => $totalAnswers > 0 ? round(($answerCounts->get(2, 0) / $totalAnswers) * 100, 1) : 0,
                '3' => $totalAnswers > 0 ? round(($answerCounts->get(3, 0) / $totalAnswers) * 100, 1) : 0,
                '4' => $totalAnswers > 0 ? round(($answerCounts->get(4, 0) / $totalAnswers) * 100, 1) : 0,
                '5' => $totalAnswers > 0 ? round(($answerCounts->get(5, 0) / $totalAnswers) * 100, 1) : 0,
            ];
        }

        // 質問ID=8の満足度を計算
        $satisfactionScore = FeedbackAnswer::where('survey_id', $survey->id)
            ->where('feedback_question_id', 8)
            ->where('department_id', $survey->department_id)
            ->avg('answer');

        // 満足度を0〜100のスケールに変換
        $satisfactionPercentage = $satisfactionScore !== null ? round(($satisfactionScore / 5) * 100, 1) : 0;

        return view('tracking', compact(
            'departments',
            'currentDeptId',
            'recommendations',
            'bottleneckTitle',
            'bottleneckScore',
            'bottleneck',
            'latestSatisfaction',
            'feedback',
            'milestones',
            'pulseSurvey',
            'tasks',
            'items',
            'latestProposal',
            'survey',
            'userCount',
            'answeredUsersCount',
            'responseRate',
            'questions',
            'questionAnswers',
            'satisfactionPercentage'
        ));
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $selectedDeptId = (int)$request->query('dept_id', $user->department_id);
        $latestProposal = Proposal::where('department_id', $selectedDeptId)
                    ->orderBy('created_at', 'desc')
                    ->first();
        $proposal = $latestProposal ? $latestProposal->id : null;

        $proposalId = $proposal;
        $name = $request->input('name');
        $date = $request->input('date');
        $id = $request->input('id');
    
        if (!$name || !$date) {
            return response()->json(['message' => 'Both name and date are required.'], 400);
        }
    
        if ($id) {
            // 既存レコードを更新
            DB::table('tasks')->where('id', $id)->update([
                'proposal_id' => $proposalId,
                'name' => $name,
                'date' => $date
            ]);
            return response()->json(['message' => 'Updated', 'id' => $id]);
        } else {
            // 新規追加
            $newId = DB::table('tasks')->insertGetId([
                'proposal_id' => $proposalId,
                'name' => $name,
                'date' => $date
            ]);
            return response()->json(['message' => 'Inserted', 'id' => $newId]);
        }
    }
    public function delete(Request $request)
    {
        $id = $request->input('id');
    
        if ($id) {
            DB::table('tasks')->where('id', $id)->delete();
            return response()->json(['message' => 'Deleted']);
        }
    
        return response()->json(['message' => 'No ID provided'], 400);
    }

}
