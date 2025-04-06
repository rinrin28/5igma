<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AIService
{
    protected SurveyService $surveyService;

    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    /**
     * AI提案を取得
     *
     * @return string JSON文字列
     */
    public function recommend(?int $surveyId, int $departmentId): string
    {
        if (! $surveyId) {
            return $this->emptyResponse();
        }

        return $this->generateRecommendation($surveyId, $departmentId);
    }

    /**
     * AI用プロンプト生成・呼び出し
     */
    private function generateRecommendation(int $surveyId, int $departmentId): string
    {
        // SurveyService からボトルネック取得
        $bottleneck = $this->surveyService->getLatestBottleneck($surveyId, $departmentId);
        if (! $bottleneck) {
            return $this->emptyResponse();
        }

        // SurveyService でサブ項目平均取得
        $subAverages = $this->surveyService->getSubAverages($surveyId, $bottleneck['category_id']);
        $prompt = $this->buildPrompt($departmentId, $bottleneck, $subAverages);

        return $this->callOpenAI($prompt);
    }

    /**
     * OpenAI API を呼び出して結果を返す
     *
     * @param  string  $prompt  プロンプト内容
     * @return string AI の応答（JSON 文字列）
     */
    private function callOpenAI(string $prompt): string
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.5,
                'max_tokens' => 800,
                'presence_penalty' => 0.3,
                'frequency_penalty' => 0.3,
            ]);

            $content = trim($response->choices[0]->message->content);
            
            // JSONとして解析可能か確認
            $decoded = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response from OpenAI', [
                    'content' => $content,
                    'json_error' => json_last_error_msg()
                ]);
                return $this->emptyResponse();
            }

            // recommendations配列の存在確認
            if (!isset($decoded['recommendations']) || !is_array($decoded['recommendations'])) {
                Log::error('Invalid recommendations format from OpenAI', [
                    'decoded' => $decoded
                ]);
                return $this->emptyResponse();
            }

            // 各recommendationの形式確認
            foreach ($decoded['recommendations'] as $rec) {
                if (!isset($rec['title']) || !isset($rec['description'])) {
                    Log::error('Invalid recommendation format from OpenAI', [
                        'recommendation' => $rec
                    ]);
                    return $this->emptyResponse();
                }
            }

            return $content;
        } catch (\Exception $e) {
            Log::error('OpenAI API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->emptyResponse();
        }
    }

    /**
     * AI へ送る日本語プロンプトを組み立てる
     *
     * @param  int  $departmentId  部署ID
     * @param  array  $bottleneck  ['title'=>項目名, 'score'=>数値]
     * @param  array  $subAverages  サブ項目平均スコアの配列
     * @return string 完成したプロンプト
     */
    private function buildPrompt(int $departmentId, array $bottleneck, array $subAverages): string
    {
        $deptName = Department::find($departmentId)->name;
        $prompt = "あなたは業務改善のプロフェッショナルAI『Buddy』として、{$deptName}の業務改善を支援します。
現状の課題と改善機会を分析し、具体的で実行可能な改善施策を3つ提案してください。

出力形式：
{
  \"recommendations\": [
    {
      \"title\": \"改善施策のタイトル（具体的なアクションを含む30文字以内）\",
      \"description\": \"施策の詳細な実施手順と期待効果（100文字程度）\"
    }
  ]
}

現状分析：
- 主要課題: {$bottleneck['title']} (現在のスコア: {$bottleneck['score']})
- 関連する指標:\n";
        foreach ($subAverages as $sub) {
            $prompt .= "  * {$sub['name']}: {$sub['avg_score']}\n";
        }

        $prompt .= "\n提案作成の要件：
1. 提案タイトル（必須要素）:
   - 具体的なアクションを明示（例：「週次1on1ミーティングの導入」）
   - 目的を含める（例：「コミュニケーション強化のための」）
   - 30文字以内で簡潔に

2. 提案内容（必須要素）:
   - 実施手順：具体的なステップを明記
   - 実施時期・頻度：明確な時間軸を設定
   - 必要なリソース：人員、時間、コストの目安
   - 期待効果：数値目標を含む具体的な成果

3. 提案の多様性：
   - 短期（1ヶ月以内）、中期（3ヶ月以内）、長期（6ヶ月以上）の異なる時間軸
   - 低コスト・中コスト・高コストの異なるリソースレベル
   - 異なるアプローチ方法（プロセス改善、ツール導入、教育研修など）

4. {$deptName}の特性を考慮：
   - 部署の特性に合わせた具体的な施策
   - 実現可能性の高い提案
   - 明確な効果測定方法

各提案は、実施主体、具体的なアクション、期待される効果を明確に示してください。";

        return $prompt;
    }

    /**
     * 空レスポンス */
    private function emptyResponse(): string
    {
        return json_encode([
            'recommendations' => [
                [
                    'title' => '提案を生成できませんでした',
                    'description' => '申し訳ありませんが、現在提案を生成できません。しばらく時間をおいて再度お試しください。'
                ],
                [
                    'title' => '提案を生成できませんでした',
                    'description' => '申し訳ありませんが、現在提案を生成できません。しばらく時間をおいて再度お試しください。'
                ],
                [
                    'title' => '提案を生成できませんでした',
                    'description' => '申し訳ありませんが、現在提案を生成できません。しばらく時間をおいて再度お試しください。'
                ]
            ]
        ]);
    }

    /**
     * 定性的フィードバックを含めた再提案を取得
     *
     * @param  int  $surveyId  最新アンケートID
     * @param  int  $departmentId  部署ID
     * @param  array  $feedback  管理職からのフィードバックデータ
     * @return string JSON形式の改善提案
     */
    public function recommendWithFeedback(int $surveyId, int $departmentId, array $feedback): string
    {
        $bottleneck = $this->surveyService->getLatestBottleneck($surveyId, $departmentId);
        if (! $bottleneck) {
            return $this->emptyResponse();
        }

        $subAverages = $this->surveyService->getSubAverages($surveyId, $bottleneck['category_id']);
        $deptName = Department::find($departmentId)->name;
        $prompt = "あなたは業務改善AI『Buddy』です。{$deptName}のボトルネックと管理職からのフィードバックをもとに、具体的かつ実行可能な改善施策を3つ提案してください。以下の形式のJSONで出力してください：
{
  \"recommendations\": [
    {
      \"title\": \"改善施策のタイトル（30文字以内）\",
      \"description\": \"施策の具体的な説明（300文字程度）\"
    }
  ]
}\n";
        $prompt .= "項目: {$bottleneck['title']} (スコア {$bottleneck['score']})\nサブ平均:\n";
        foreach ($subAverages as $sub) {
            $prompt .= "- {$sub['name']}: {$sub['avg_score']}\n";
        }
        $prompt .= "現場認識: {$feedback['fieldRecognition']}\n要望: {$feedback['requestToBuddy']}\n制約: {$feedback['resourcesConstraints']}\n";
        $prompt .= "\n注意事項：\n";
        $prompt .= "- タイトルは問題解決のための具体的なアクションを含み、30文字以内にしてください\n";
        $prompt .= "- 説明文には以下の要素を必ず含めてください：\n";
        $prompt .= "  1. 具体的な実施手順（例：「週◯回の定例MTGを設定し」「専門家を招いて研修を実施し」など）\n";
        $prompt .= "  2. 実施頻度や期間（例：「毎週◯曜日」「◯ヶ月間」など）\n";
        $prompt .= "  3. 期待される具体的な効果（例：「応対時間を20%削減」「顧客満足度を15%向上」など）\n";
        $prompt .= "- 提案は必ず3つ作成し、それぞれ異なるアプローチを提示してください\n";
        $prompt .= "- 実現可能で具体的な数値目標を含めてください\n";
        $prompt .= "- 現場認識、要望、制約を考慮した実現可能な提案を行ってください\n";
        $prompt .= "- 一般的な提案ではなく、{$deptName}の特性を考慮した具体的な提案を行ってください\n";

        return $this->callOpenAI($prompt);
    }

    /**
     * 管理職のフィードバックを受けて再提案を実行する
     *
     * @param  Request  $request  HTTPリクエスト
     * @param  SurveyService  $surveyService  サーベイデータ取得サービス
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SurveyService $surveyService)
    {
        $validated = $request->validate([
            'dept_id' => 'required|integer',
            'fieldRecognition' => 'required|string|max:200',
            'requestToBuddy' => 'required|string|max:200',
            'resourcesConstraints' => 'required|string|max:200',
        ]);

        $surveyId = optional($surveyService->latestForDepartment($validated['dept_id']))->id;
        $aiJson = $this->recommendWithFeedback($surveyId, $validated['dept_id'], $validated);

        return back()->with('aiRecommendations', $aiJson);
    }

    /**
     * レスポンスが空かどうかを判定
     */
    private function isEmptyResponse(string $response): bool
    {
        $decoded = json_decode($response, true);
        if (!isset($decoded['recommendations']) || !is_array($decoded['recommendations'])) {
            return true;
        }

        foreach ($decoded['recommendations'] as $rec) {
            if ($rec['title'] === '提案を生成できませんでした') {
                return true;
            }
        }

        return false;
    }
}
