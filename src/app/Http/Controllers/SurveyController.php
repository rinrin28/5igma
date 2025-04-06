<?php

namespace App\Http\Controllers;

use App\Mail\SurveyInvitationMail;
use App\Models\Category;
use App\Models\CategoryScore;
use App\Models\Expectation;
use App\Models\FeedbackAnswer;
use App\Models\FeedbackQuestion;
use App\Models\Satisfaction;
use App\Models\SubResponse;
use App\Models\Subcategory;
use App\Models\Survey;
use App\Models\User;
use App\Services\ChartService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
    protected $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function index()
    {
        $user = Auth::user();
        $department = $user->department;
        
        // ユーザーの部署に紐づく最新のアンケートを取得
        $survey = Survey::where('department_id', $user->department_id)
            ->where('survey_types_id', 1)  // 通常のアンケートタイプ
            ->where('start_date', '<=', now())
            ->latest('start_date')
            ->firstOrFail();
            
        $categories = Category::with('subcategories')->get();

        return view('survey.index', compact('categories', 'user', 'department', 'survey'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // ユーザーの部署に紐づく最新のアンケートを取得
        $survey = Survey::where('department_id', $user->department_id)
            ->where('survey_types_id', 1)  // 通常のアンケートタイプ
            ->where('start_date', '<=', now())
            ->latest('start_date')
            ->firstOrFail();
            
        $surveyId = $survey->id;
        $userId = $user->id;

        $request->validate([
            'expectations' => 'required|array',
            'expectations.*' => 'required|integer|min:1|max:5',
            'satisfactions' => 'required|array',
            'satisfactions.*' => 'required|integer|min:1|max:5',
            'sub_responses' => 'required|array',
            'sub_responses.*' => 'required|integer|min:1|max:5',
        ]);

        foreach ($request->expectations as $categoryId => $score) {
            Expectation::updateOrCreate(
                [
                    'survey_id' => $surveyId,
                    'category_id' => $categoryId,
                    'user_id' => $userId,
                ],
                [
                    'score' => $score,
                    'is_submitted' => true,
                    'save_at' => now(),
                ]
            );
        }

        foreach ($request->satisfactions as $categoryId => $score) {
            Satisfaction::updateOrCreate(
                [
                    'survey_id' => $surveyId,
                    'category_id' => $categoryId,
                    'user_id' => $userId,
                ],
                [
                    'score' => $score,
                    'is_submitted' => true,
                    'save_at' => now(),
                ]
            );
        }

        foreach ($request->sub_responses as $subcategoryId => $score) {
            SubResponse::updateOrCreate(
                [
                    'survey_id' => $surveyId,
                    'subcategory_id' => $subcategoryId,
                    'user_id' => $userId,
                ],
                [
                    'score' => $score,
                    'is_submitted' => true,
                    'save_at' => now(),
                ]
            );
        }

        // カテゴリースコアとサブカテゴリースコアを計算
        $this->chartService->calculateCategoryScores($surveyId);
        $this->chartService->calculateSubcategoryScores($surveyId);

        // アンケート回答率を計算
        $survey = Survey::findOrFail($surveyId);
        $department = $survey->department;
        $totalUsers = User::where('department_id', $department->id)
            ->whereIn('role', ['employee', 'management','executive','admin'])
            ->count();
        
        // 提出したユーザーのIDを取得
        $submittedUserIds = Expectation::where('survey_id', $surveyId)
            ->where('is_submitted', true)
            ->whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->distinct('user_id')
            ->pluck('user_id');
            
        // 提出したユーザー数
        $submittedUsers = $submittedUserIds->count();
        
        $responseRate = $totalUsers > 0 ? ($submittedUsers / $totalUsers) * 100 : 0;
        
        // 前回の回答率を取得
        $previousResponseRate = cache()->get("survey_{$surveyId}_response_rate", 0);
        
        // 回答率の変化を計算
        $deltaRate = $responseRate - $previousResponseRate;
        
        // 回答率と変化率をキャッシュに保存
        cache()->put("survey_{$surveyId}_response_rate", $responseRate, now()->addDays(30));
        cache()->put("survey_{$surveyId}_delta_rate", $deltaRate, now()->addDays(30));

        return redirect()->route('survey.complete')->with('success', '送信完了しました');
    }

    public function saveDraft(Request $request)
    {
        try {
            $user = Auth::user();
            
            // ユーザーの部署に紐づく最新のアンケートを取得
            $survey = Survey::where('department_id', $user->department_id)
                ->where('survey_types_id', 1)  // 通常のアンケートタイプ
                ->where('start_date', '<=', now())
                ->latest('start_date')
                ->firstOrFail();
                
            $surveyId = $survey->id;
            $userId = $user->id;

            $expectations = $request->input('expectations', []);
            $satisfactions = $request->input('satisfactions', []);
            $subResponses = $request->input('sub_responses', []);

            // デバッグ用のログを追加
            Log::info('Draft save attempt:', [
                'survey_id' => $surveyId,
                'user_id' => $userId,
                'department_id' => $user->department_id,
                'expectations' => $expectations,
                'satisfactions' => $satisfactions,
                'sub_responses' => $subResponses
            ]);

            // 期待値の保存
            foreach ($expectations as $categoryId => $score) {
                if (is_numeric($score) && Category::find($categoryId)) {
                    Log::info('Saving expectation:', [
                        'category_id' => $categoryId,
                        'score' => $score
                    ]);
                    Expectation::updateOrCreate(
                        [
                            'survey_id' => $surveyId,
                            'category_id' => (int)$categoryId,
                            'user_id' => $userId,
                            'is_submitted' => false,
                        ],
                        [
                            'score' => (int)$score,
                            'save_at' => now(),
                        ]
                    );
                }
            }

            // 満足度の保存
            foreach ($satisfactions as $categoryId => $score) {
                if (is_numeric($score) && Category::find($categoryId)) {
                    Log::info('Saving satisfaction:', [
                        'category_id' => $categoryId,
                        'score' => $score
                    ]);
                    Satisfaction::updateOrCreate(
                        [
                            'survey_id' => $surveyId,
                            'category_id' => (int)$categoryId,
                            'user_id' => $userId,
                            'is_submitted' => false,
                        ],
                        [
                            'score' => (int)$score,
                            'save_at' => now(),
                        ]
                    );
                }
            }

            // サブカテゴリー回答の保存
            foreach ($subResponses as $subcategoryId => $score) {
                if (is_numeric($score) && Subcategory::find($subcategoryId)) {
                    Log::info('Saving sub response:', [
                        'subcategory_id' => $subcategoryId,
                        'score' => $score
                    ]);
                    SubResponse::updateOrCreate(
                        [
                            'survey_id' => $surveyId,
                            'subcategory_id' => (int)$subcategoryId,
                            'user_id' => $userId,
                            'is_submitted' => false,
                        ],
                        [
                            'score' => (int)$score,
                            'save_at' => now(),
                        ]
                    );
                }
            }

            Log::info('Draft save completed successfully');
            return response()->json([
                'success' => true,
                'message' => '下書きを保存しました。',
                'saved_at' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Draft save error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => '下書きの保存に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showScores($surveyId)
    {
        $scores = CategoryScore::with('category')
            ->where('survey_id', $surveyId)
            ->get();

        return view('survey.scores', compact('scores'));
    }

    public function period()
    {
        return view('survey_period');
    }

    public function sendInvitation(Request $request)
    {
        $manager = Auth::user();
        $departmentId = $manager->department_id;

        $recipients = $recipients = User::where('department_id', $departmentId)
        ->whereIn('role', ['employee', 'management'])
        ->orWhere('id', $manager->id)
        ->get();

        $survey = Survey::where('department_id', $manager->department_id)->latest()->first();
        if (! $survey) {
            return redirect()->back()->with('error', '送信対象のアンケートが見つかりません。');
        }

        $startDate = Carbon::parse($survey->start_date);
        $endDate = Carbon::parse($survey->end_date);
        $today = Carbon::today();

        // 今日の日付が開始日と終了日の間に含まれているかチェック
        if (!$today->between($startDate, $endDate)) {
            return redirect()->back()->with('error', 'アンケートの実施期間外です。');
        }

        $surveyUrl = route('survey.start', $survey->id);

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new SurveyInvitationMail(
                $surveyUrl,
                $recipient,
                $survey->start_date,
                $survey->end_date
            ));
        }

        return redirect()->route('survey.period')->with('success', 'アンケートメールを送信しました。');
    }

    public function start()
    {
        $user = Auth::user();
        $department = $user->department;
        
        // ユーザーの部署に紐づく最新のアンケートを取得
        $survey = Survey::where('department_id', $user->department_id)
            ->where('survey_types_id', 1)  // 通常のアンケートタイプ
            ->where('start_date', '<=', now())
            ->latest('start_date')
            ->firstOrFail();

        return view('survey.survey_start', compact('survey', 'user', 'department'));
    }

    public function complete()
    {
        $user = Auth::user();
        $department = $user->department;
        
        // ユーザーの部署に紐づく最新のアンケートを取得
        $survey = Survey::where('department_id', $user->department_id)
            ->where('survey_types_id', 1)  // 通常のアンケートタイプ
            ->where('start_date', '<=', now())
            ->latest('start_date')
            ->firstOrFail();

        return view('survey.complete', compact('user', 'department', 'survey'));
    }


    // ここからトラッキング画面用です
    public function showSurveyResult()
    {
        // 暫定的に survey_id=27 のデータを取得
        // 本来は Auth::user() から取得した department_id を使用する
        // survey_id=27 のデータを取得
        $survey = Survey::find(27);

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

        // ビューにデータを渡す
        return view('unique', compact(
            'survey',
            'userCount',
            'answeredUsersCount',
            'responseRate',
            'questions',
            'questionAnswers',
            'satisfactionPercentage'
        ));
    }
    public function resetAnswers(Survey $survey)
    {
            // 未提出の回答を削除
            Expectation::where('user_id', auth()->id())
                ->where('survey_id', $survey->id)
                ->where('is_submitted', false)
                ->delete();

            Satisfaction::where('user_id', auth()->id())
                ->where('survey_id', $survey->id)
                ->where('is_submitted', false)
                ->delete();

            SubResponse::where('user_id', auth()->id())
                ->where('survey_id', $survey->id)
                ->where('is_submitted', false)
                ->delete();

                return redirect()->route('survey.index')->with('success', 'アンケートを開始します。');
        
    }
}
