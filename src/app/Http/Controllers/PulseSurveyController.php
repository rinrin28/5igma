<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\FeedbackQuestion;
use App\Models\FeedbackAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PulseSurveyController extends Controller
{
    /**
     * パルスサーベイのスタート画面を表示
     */
    public function start(Survey $survey)
    {
        $user = Auth::user();
        $department = $user->department;

        // 既存の回答があるかチェック
        $hasExistingAnswers = FeedbackAnswer::where('user_id', $user->id)
            ->where('survey_id', $survey->id)
            ->exists();

        return view('pulse-survey.start', compact('survey', 'user', 'department', 'hasExistingAnswers'));
    }

    /**
     * パルスサーベイのフォームを表示
     */
    public function index(Survey $survey)
    {
        $user = Auth::user();
        $department = $user->department;
        $questions = FeedbackQuestion::all();

        // 既存の回答があれば取得
        $existingAnswers = FeedbackAnswer::where('user_id', $user->id)
            ->where('survey_id', $survey->id)
            ->pluck('answer', 'feedback_question_id');

        return view('pulse-survey.index', compact('survey', 'user', 'department', 'questions', 'existingAnswers'));
    }

    /**
     * パルスサーベイの回答を保存
     */
    public function store(Request $request, Survey $survey)
    {
        try {
            $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required|integer|min:1|max:5',
            ]);

            DB::beginTransaction();

            $user = Auth::user();

            // すべての質問に回答があるか確認
            $questions = FeedbackQuestion::count();
            if (count($request->answers) !== $questions) {
                throw new \Exception('すべての質問に回答してください');
            }

            // 既存の回答を更新
            foreach ($request->answers as $questionId => $answer) {
                $feedbackAnswer = FeedbackAnswer::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'survey_id' => $survey->id,
                        'feedback_question_id' => $questionId,
                    ],
                    [
                        'answer' => $answer,
                        'department_id' => $user->department_id,
                        'is_completed' => true  // 完了フラグを立てる
                    ]
                );

            }


            DB::commit();

            return redirect()
                ->route('pulse-survey.complete')
                ->with([
                    'success' => 'パルスサーベイの回答が完了しました',
                    'survey_id' => $survey->id
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * 完了画面を表示
     */
    public function complete()
    {
        $user = Auth::user();
        $department = $user->department;

        // セッションからサーベイIDを取得、なければ最新のサーベイを使用
        $surveyId = session('survey_id');
        $survey = $surveyId ? Survey::find($surveyId) : Survey::latest()->first();

        if (!$survey) {
            $survey = Survey::latest()->first();
        }

        return view('pulse-survey.complete', compact('user', 'department', 'survey'));
    }

    /**
     * 自動保存用のエンドポイント
     */
    public function autoSave(Request $request, Survey $survey)
    {
        try {
            $request->validate([
                'question_id' => 'required|exists:feedback_questions,id',
                'answer' => 'required|integer|min:1|max:5',
            ]);

            $user = Auth::user();

            // 途中保存時はis_completed = false
            FeedbackAnswer::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'survey_id' => $survey->id,
                    'feedback_question_id' => $request->question_id,
                ],
                [
                    'answer' => $request->answer,
                    'department_id' => $user->department_id,
                    'is_completed' => false // 途中保存
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => '回答が保存されました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '回答の保存に失敗しました'
            ], 500);
        }
    }

    /**
     * 集計用のメソッド（完了した回答のみを対象）
     */
    public function aggregate(Survey $survey)
    {
        // 完了した回答のみを集計
        $results = FeedbackAnswer::where('survey_id', $survey->id)
            ->where('is_completed', true)  // 完了した回答のみ
            ->select(
                'feedback_question_id',
                DB::raw('AVG(answer) as average_score'),
                DB::raw('COUNT(*) as response_count')
            )
            ->groupBy('feedback_question_id')
            ->get();

        return $results;
    }
}
