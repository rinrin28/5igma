<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyTypes;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyInvitationMail;

class AdminController extends Controller
{
    public function index()
    {
        // 最新のアンケート設定を取得
        $firstHalfSurvey = Survey::whereMonth('start_date', '>=', 4)
            ->whereMonth('start_date', '<=', 9)
            ->latest()
            ->first();
        
        $secondHalfSurvey = Survey::where(function($query) {
            $query->whereMonth('start_date', '>=', 10)
                  ->orWhereMonth('start_date', '<=', 3);
        })
            ->latest()
            ->first();

        return view('admin', compact('firstHalfSurvey', 'secondHalfSurvey'));
    }

    public function updateSurveyPeriod(Request $request)
    {
        $request->validate([
            'first_half_start' => 'required|date',
            'second_half_start' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            // 上半期のアンケート作成
            $firstHalfStart = Carbon::parse($request->first_half_start);
            $firstHalfEnd = $firstHalfStart->copy()->addDays(13); // 2週間（開始日を含むので13日後）

            $firstHalfSurveys = $this->createSurvey(
                $firstHalfStart,
                $firstHalfEnd
            );

            // 下半期のアンケート作成
            $secondHalfStart = Carbon::parse($request->second_half_start);
            $secondHalfEnd = $secondHalfStart->copy()->addDays(13); // 2週間（開始日を含むので13日後）

            $secondHalfSurveys = $this->createSurvey(
                $secondHalfStart,
                $secondHalfEnd
            );
            
            // 新しく作成したアンケートに対してのみメールを送信
            $this->sendSurveyInvitationsForNewSurveys($firstHalfSurveys);
            $this->sendSurveyInvitationsForNewSurveys($secondHalfSurveys);
        });

        return redirect()->route('admin')->with('success', 'アンケート期間を設定しました。');
    }

    private function createSurvey($startDate, $endDate)
    {
        $surveyType = SurveyTypes::where('title', '全社組織改善アンケート')->first();
        $departments = Department::all();
        $createdSurveys = [];

        foreach ($departments as $department) {
            $survey = Survey::create([
                'department_id' => $department->id,
                'survey_types_id' => $surveyType->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
            $createdSurveys[] = $survey;
        }
        
        return $createdSurveys;
    }

    private function sendSurveyInvitationsForNewSurveys($surveys)
    {
        $today = Carbon::today();
        
        foreach ($surveys as $survey) {
            // アンケートの開始日が今日以前で、終了日が今日以降の場合のみメールを送信
            if ($survey->start_date->lte($today) && $survey->end_date->gte($today)) {
                // 全ユーザーを取得
                $recipients = User::whereIn('role', ['employee', 'management'])
                    ->get();
                
                // 各ユーザーにメールを送信
                foreach ($recipients as $recipient) {
                    $surveyUrl = route('survey.start', $survey->id);
                    
                    Mail::to($recipient->email)->send(new SurveyInvitationMail(
                        $surveyUrl,
                        $recipient,
                        $survey->start_date,
                        $survey->end_date
                    ));
                }
            }
        }
    }
}