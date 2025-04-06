<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>施策パルスサーベイ</title>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+Antique:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .satisfaction-circle[data-value="1"] {
            border-color: #FF768D;
        }
        .satisfaction-circle[data-value="2"] {
            border-color: #FFABB9;
        }
        .satisfaction-circle[data-value="3"] {
            border-color: #DADEE5;
        }
        .satisfaction-circle[data-value="4"] {
            border-color: #BCFFF5;
        }
        .satisfaction-circle[data-value="5"] {
            border-color: #99F7E9;
        }
        .satisfaction-circle.selected[data-value="1"] {
            background-color: #FF768D !important;
            border-color: #FF768D !important;
            color: white !important;
        }
        .satisfaction-circle.selected[data-value="2"] {
            background-color: #FFABB9 !important;
            border-color: #FFABB9 !important;
            color: white !important;
        }
        .satisfaction-circle.selected[data-value="3"] {
            background-color: #DADEE5 !important;
            border-color: #DADEE5 !important;
            color: white !important;
        }
        .satisfaction-circle.selected[data-value="4"] {
            background-color: #BCFFF5 !important;
            border-color: #BCFFF5 !important;
            color: white !important;
        }
        .satisfaction-circle.selected[data-value="5"] {
            background-color: #99F7E9 !important;
            border-color: #99F7E9 !important;
            color: white !important;
        }

        /* 375px〜425pxの間でレスポンシブ対応 */
        @media (min-width: 375px) and (max-width: 425px) {
            /* ヘッダーのスタイル */
            header {
                height: auto !important;
                padding: 8px 12px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .header-title {
                font-size: 18px;
                margin-bottom: 2px;
            }

            .header-user-info {
                font-size: 12px;
                display: flex;
                flex-wrap: wrap;
                gap: 2px;
            }

            .header-user-info span {
                margin-right: 2px;
            }

            /* メインコンテンツのスタイル */
            main {
                padding-top: 100px;
                padding-left: 12px;
                padding-right: 12px;
            }

            /* 質問カードのスタイル */
            .question-card {
                margin-bottom: 16px;
                padding: 16px;
            }

            .question-text {
                font-size: 16px;
                line-height: 1.3;
            }

            /* 満足度サークル */
            .satisfaction-circle {
                width: 40px !important;
                height: 40px !important;
                border-width: 4px !important;
                font-size: 12px !important;
            }

            .circle-label {
                font-size: 10px;
            }

            /* 送信ボタン */
            .submit-button {
                padding: 10px 20px;
                font-size: 16px;
            }

            /* 保存ステータス */
            #saveStatus {
                top: 90px;
                left: 50%;
                transform: translateX(-50%);
            }
            .yesno {
                font-size: 8px !important; /* フォントサイズを10pxに変更 */
            }

            }
    </style>
</head>
<body class="bg-customGray">
    <header class="w-full h-[60px] bg-[#E8EBF0] shadow-sm rounded-br-3xl rounded-bl-3xl fixed top-0 left-0 z-[80] ">
        <div class="max-w-screen-xl mx-auto px-6 h-full flex items-center justify-between header-content">
            <h1 class="text-2xl font-bold text-customPink header-title">パルスサーベイ</h1>
            <div class="flex items-center space-x-2 text-customNavy header-user-info">
                <span class="text-lg">金堂印刷株式会社</span>
                <span class="text-customNavy">/</span>
                <span class="text-sm">{{ $department->name ?? '部署なし' }}</span>
                <span class="text-customNavy ml-4">{{ $user->name ?? 'ユーザー不明' }}</span>
                <span class="text-customNavy">さん</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-lg font-semibold text-customNavy">{{ $survey->year ?? date('Y') }}</span>
                <span class="text-sm text-customNavy">{{ $survey->period ?? '上半期' }}</span>
            </div>
        </div>
    </header>

    <!-- 保存ステータス -->
    <div id="saveStatus" class="fixed top-[120px] left-[150px] py-2 px-4 rounded-full bg-white shadow-sm text-sm hidden z-[90]">
        <span class="flex items-center">
            <img src="{{ asset('image/store.png') }}" alt="check" class="w-4 h-4 mr-2">
            保存済み
        </span>
    </div>

    <main class="pt-[80px] px-6 mx-auto bg-customGray w-screen h-[2800px] rounded-3xl mt-[100px]">
        <form method="POST" action="{{ route('pulse-survey.store', $survey) }}" class="space-y-6">
            @csrf
            @foreach($questions as $question)
                <div class="mb-8 bg-white rounded-xl p-8 shadow-sm">
                    <div class="flex flex-col items-center">
                        <h3 class="text-xl font-bold mb-4 text-center text-customNavy">
                            {{ $question->question }}
                        </h3>
                        <div class="flex flex-col items-center w-full max-w-4xl mt-0 md:mt-4">
                            <div class="flex items-center justify-between w-full">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="flex flex-col items-center">
                                        <div class="satisfaction-circle w-16 h-16 md:w-24 md:h-24 rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 flex items-center justify-center text-[#82868B] text-lg {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == $i ? 'selected' : '' }}"
                                            data-value="{{ $i }}"
                                            data-question-id="{{ $question->id }}">
                                        </div>
                                        @if($i === 1)
                                            <span class="yesno text-[14px] text-customNavy mt-2">そう思わない</span>
                                        @elseif($i === 5)
                                            <span class="yesno text-[14px] text-customNavy mt-2">そう思う</span>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <input type="hidden" name="answers[{{ $question->id }}]" id="answer_{{ $question->id }}" value="{{ $existingAnswers[$question->id] ?? '' }}">
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="bottom-0 left-0 w-full p-4 items-center">
                <div class="max-w-screen-xl mx-auto flex justify-center">
                    <button type="submit" class="bg-customPink text-white px-12 py-4 rounded-xl text-lg font-bold hover:opacity-90">
                        回答を送信する
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const circles = document.querySelectorAll('.satisfaction-circle');
            const saveStatus = document.getElementById('saveStatus');
            const surveyId = '{{ $survey->id }}';

            circles.forEach(circle => {
                circle.addEventListener('click', function () {
                    const questionId = this.dataset.questionId;
                    const value = this.dataset.value;

                    // 同じ質問の他の選択肢のスタイルをリセット
                    document.querySelectorAll(`[data-question-id="${questionId}"]`).forEach(el => {
                        el.classList.remove('selected');
                    });

                    // 選択された選択肢のスタイルを変更
                    this.classList.add('selected');

                    // hidden inputに値を設定
                    document.getElementById(`answer_${questionId}`).value = value;

                    // 自動保存を実行
                    autoSave(questionId, value);

                    // 次の設問にスクロール
                    scrollToNextQuestion(questionId);
                });
            });

            function autoSave(questionId, value) {
                const token = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/pulse-survey/${surveyId}/auto-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: value,
                        is_completed: true // 完了フラグを送信
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        saveStatus.classList.remove('hidden');
                        setTimeout(() => {
                            saveStatus.classList.add('hidden');
                        }, 2000);
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            function scrollToNextQuestion(currentQuestionId) {
                // 現在の質問の次の質問を取得
                const currentQuestionElement = document.querySelector(`[data-question-id="${currentQuestionId}"]`);
                const nextQuestionElement = currentQuestionElement.closest('.mb-8').nextElementSibling;

                if (nextQuestionElement) {
                    // 次の質問を画面の中央にスクロール
                    nextQuestionElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
    </script>
</body>
</html>
