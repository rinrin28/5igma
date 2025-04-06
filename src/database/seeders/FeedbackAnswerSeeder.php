<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeedbackAnswer;
use App\Models\Survey;
use App\Models\FeedbackQuestion;
use App\Models\User;

class FeedbackAnswerSeeder extends Seeder
{
    public function run()
    {
        // survey_types_id = 2 のサーベイを取得
        $surveys = Survey::where('survey_types_id', 2)->get();

        // 既存の質問を取得
        $questions = FeedbackQuestion::all();

        // user_id >= 9 のユーザーを取得
        $filteredUsers = User::where('id', '>=', 9)->get();

        // サーベイごとに回答を作成
        foreach ($surveys as $survey) {
            // サーベイに関連する部署のすべてのユーザーを取得
            $users = $filteredUsers->where('department_id', $survey->department_id);

            foreach ($users as $user) {
                foreach ($questions as $question) {
                    FeedbackAnswer::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'survey_id' => $survey->id,
                            'feedback_question_id' => $question->id,
                            'department_id' => $survey->department_id,
                        ],
                        [
                            'answer' => rand(1, 5), // ランダムな五段階評価
                            'is_completed' => true,
                        ]
                    );
                }
            }
        }
    }
}
