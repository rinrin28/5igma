<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\AIService;
use App\Services\SurveyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShortTermGoal;
use App\Models\PulseSurvey;
use App\Models\Survey;
use App\Models\Task;
use App\Models\Proposal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class PlanningController extends Controller
{
    public function index(Request $request, SurveyService $surveyService, AIService $aiService)
    {
        $user = Auth::user();
        $selectedDeptId = (int) $request->query('dept_id', $user->department_id);

        // 選択部署の最新 Survey モデルを取得
        $currentSurvey = $surveyService->latestForDepartment($selectedDeptId);
        $surveyId = $currentSurvey?->id;

        // ボトルネック項目 (タイトル＋スコア) を取得
        $bottleneck = $surveyService->getLatestBottleneck($surveyId, $selectedDeptId);
        $bottleneckTitle = $bottleneck['title'];
        $bottleneckScore = $bottleneck['score'];

        // セッションから読み出し（なければ空配列／文字列）
        $feedback = session('planningFeedback', []);
        $aiJson = session('aiRecommendations', '');
        $aiData = json_decode($aiJson, true) ?: [];

        $raw = $aiData['recommendations'] ?? $aiData;

        $recommendations = collect($raw)
            ->map(fn ($item, $key) => [
                'id' => (int)$key + 1,
                'title' => is_array($item) ? ($item['title'] ?? "提案".((int)$key + 1)) : "提案".((int)$key + 1),
                'description' => is_array($item) 
                    ? ($item['description'] ?? '') 
                    : (is_string($item) ? $item : ''),
            ])
            ->values()
            ->toArray();

        // 全部署リスト (選択切替用)
        $departments = Department::orderBy('id')->get();
        $currentDeptId = $selectedDeptId;
        $currentSurveyEndDate = Carbon::parse($currentSurvey->end_date);
        $endDate = $currentSurveyEndDate->format('Y/m/d');
        $taskDate = $currentSurveyEndDate->format('Y-m-d');
        $fiveMonthsLater = $currentSurveyEndDate->addMonths(5)->endOfMonth()->format('Y/m/d');

        return view('planning', compact(
            'departments',
            'currentDeptId',
            'recommendations',
            'bottleneckTitle',
            'bottleneckScore',
            'bottleneck',
            'feedback',
            'endDate',
            'fiveMonthsLater'
        ));
    }

    public function update(Request $request, SurveyService $surveyService, AIService $aiService)
    {
        try {
            $validated = $request->validate([
                'dept_id' => 'required|integer|exists:departments,id',
                'fieldRecognition' => 'nullable|string|max:200',
                'requestToBuddy' => 'nullable|string|max:200',
                'resourcesConstraints' => 'nullable|string|max:200',
            ]);

            $deptId = $validated['dept_id'];
            $surveyId = optional($surveyService->latestForDepartment($deptId))->id;

            if (!$surveyId) {
                throw new \Exception('調査データが見つかりませんでした。');
            }

            $hasFeedback = collect($validated)
                ->except('dept_id')
                ->filter(fn ($v) => ! empty($v))
                ->isNotEmpty();

            $aiJson = $hasFeedback
                ? $aiService->recommendWithFeedback($surveyId, $deptId, $validated)
                : $aiService->recommend($surveyId, $deptId);

            if (empty($aiJson)) {
                throw new \Exception('AIからの提案を生成できませんでした。');
            }

            $recommendations = json_decode($aiJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('AIからの応答を解析できませんでした。エラー: ' . json_last_error_msg());
            }

            session()->put('aiRecommendations', $aiJson);
            session()->put('planningFeedback', $validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'recommendations' => $recommendations['recommendations'] ?? [],
                    'message' => $hasFeedback 
                        ? 'フィードバックを考慮した提案が生成されました'
                        : '基本的な提案が生成されました'
                ]);
            }

            return redirect()->route('planning', ['dept_id' => $deptId]);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: '提案の生成中にエラーが発生しました。もう一度お試しください。'
                ], 422);
            }

            return redirect()
                ->route('planning', ['dept_id' => $deptId])
                ->with('error', $e->getMessage() ?: '提案の生成中にエラーが発生しました。もう一度お試しください。');
        }
    }

    public function storeAll(Request $request, SurveyService $surveyService)
    {
        $user = Auth::user();
        $selectedDeptId = (int)$request->query('dept_id', $user->department_id);

        $currentSurvey = $surveyService->latestForDepartment($selectedDeptId);
        $surveyId = $currentSurvey?->id;

        // ボトルネック項目 (タイトル＋スコア) を取得
        $bottleneck = $surveyService->getLatestBottleneck($surveyId, $selectedDeptId);

        $title = $request->input('proposal_title');
        $description = $request->input('proposal_description');
        $satisfaction = $request->input('satisfaction');

        $proposal = Proposal::create([
            'category_id' => $bottleneck['category_id'],
            'proposal' => $title,
            'description' => $description,
            'target_score' => $satisfaction,
            'is_active' => 1,
            'department_id' => $user->department_id,
            'survey_id' => $surveyId
        ]);

        $milestones = json_decode($request->milestones, true);
        $surveyStart = $request->survey_start_date;
        $surveyEnd = $request->survey_end_date;

        foreach ($milestones as $milestone) {
            Task::create([
                'proposal_id' => $proposal->id,
                'name' => $milestone['title'],
                'date' => $milestone['date'],
                'status' => '未実施'
            ]);
        }

        $survey = Survey::create([
            'department_id' => auth()->user()->department_id,
            'survey_types_id' => 2,
            'start_date' => $surveyStart,
            'end_date' => $surveyEnd,
        ]);

        $pulseSurvey = PulseSurvey::create([
            'proposal_id' => $proposal->id,
            'survey_id' => $survey->id
        ]);

        ShortTermGoal::create([
            'pulse_survey_id' => $pulseSurvey->id,
            'target_score' => $satisfaction
        ]);

        return redirect()->route('tracking')->with('message', '施策計画が保存されました');
    }

}
