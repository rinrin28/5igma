<?php
namespace App\Http\Controllers;

use App\Services\SurveyService;
use Illuminate\Http\Request;
use App\Models\ShortTermGoal;
use App\Models\PulseSurvey;
use App\Models\Survey;
use App\Models\Task;
use App\Models\Proposal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{

    public function store(Request $request, SurveyService $surveyService) {

        $user = Auth::user();

        $selectedDeptId = (int)$request->query('dept_id', $user->department_id);

        $currentSurvey = $surveyService->latestForDepartment($selectedDeptId);
        $surveyId = $currentSurvey?->id;
    
        // ボトルネック項目 (タイトル＋スコア) を取得
        $bottleneck = $surveyService->getLatestBottleneck($surveyId, $selectedDeptId);

        $targetScore = $request->input('satisfaction');
        $pulseSurveyStart = $request->input('pulseSurveyStart');
        $pulseSurveyEnd = $request->input('pulseSurveyEnd');
        $milestones = $request->input('milestones');


        $proposal = Proposal::create([
            'category_id' => 1,
            'subcategory_id' => 1,
            'proposal' => '1on1ミーティング',
            'target_score' => 80,
            'is_active' => 1,
            'department_id' => $user->department_id,
            'survey_id' => $surveyId
        ]);

        $survey = Survey::create([
            'department_id' => auth()->user()->department_id,
            'survey_types_id' => 2,
            'start_date' => $pulseSurveyStart,
            'end_date' => $pulseSurveyEnd,
        ]);

        $pulseSurvey = PulseSurvey::create([
            'proposal_id' => $proposal->id,
            'survey_id' => $survey->id
        ]);

        ShortTermGoal::create([
            'pulse_survey_id' => $pulseSurvey->id,
            'target_score' => $targetScore
        ]);

        foreach ($request->milestones as $milestone) {
            Task::create([
                'proposal_id' => $proposal->id,
                'name' => $milestone['name'],
                'date' => $milestone['date'],
                'status' => '未実施'
            ]);
        }
    
        return redirect()->route('tracking')->with('message', '施策計画が保存されました');
    }
}
