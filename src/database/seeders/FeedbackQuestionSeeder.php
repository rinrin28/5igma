<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeedbackQuestion;

class FeedbackQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => '現在、実施中の組織改善施策は計画通りに進んでいると感じますか？',
            ],
            [
                'question' => '施策の目的や具体的な目標は、チーム内で十分に共有され、理解されていますか？',
            ],
            [
                'question' => '管理職は、施策の実行において積極的にリードし、必要な支援を提供していると感じますか？',
            ],
            [
                'question' => '現場からの意見・フィードバックは、施策の改善に適切に反映されていると感じますか？',
            ],
            [
                'question' => '施策実行中に、具体的な課題や障害が明確に認識され、対策が講じられていると感じますか？',
            ],
            [
                'question' => '施策の実施によって、組織内のコミュニケーションや業務効率、チームワークなどに具体的な改善が実感できていますか？',
            ],
            [
                'question' => '現時点で、組織改善施策全体に対して満足していますか？',
            ],
            [
                'question' => '現時点で「今取り組んでいるボトルネック」に対して満足していますか？',
            ],
            [
                'question' => 'この施策は今後も継続すべきだと思いますか？',
            ],
        ];

        foreach ($questions as $question) {
            FeedbackQuestion::create($question);
        }
    }
}
