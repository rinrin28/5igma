<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIzBuddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+Antique:wght@700&display=swap" rel="stylesheet">
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-gray-100">
{{-- 質問回答結果 --}}
    <div>
      <div class="max-w-[853px] mx-auto bg-white shadow-md rounded-[26px] p-6 my-8 border-5 !border-customPink">
        <!-- ヘッダ部分 -->
        <div class="flex items-center justify-between mb-4">
              <div>
                                    <h1 class="text-3xl font-bold text-customPink">
                        パルスサーベイ 調査結果
                    </h1>
                    <p class="text-lg text-gray-500">
                        施策の浸透状況に関するアンケートの調査結果です
                    </p>
        </div>
                <div class="text-sm text-gray-500 flex items-center font-bold">
            <img src="{{ asset('image/calendar.png') }}" alt="Calendar" class="w-6 h-6 mr-2">
            〜{{ $survey->end_date->format('Y/m/d') }}
        </div>
        </div>
        <div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
        <!-- 集計情報 -->
                    <div class="flex flex-wrap items-end gap-4 mb-3">
                            <div class="flex flex-wrap items-end gap-4 mb-3">
                    <div class="flex items-end">
                        <div class="flex items-end">
                            <span class="text-sm text-customGray3 mr-1">有効回答数</span>
                            <span class="text-3xl font-semibold text-customGray3">{{ $answeredUsersCount }}</span>
                            <span class="text-sm text-customGray3">/{{ $userCount }}件</span>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <span class="text-sm text-customGray3 mr-1">回答率</span>
                        <span class="text-3xl font-semibold text-customGray3">{{ $responseRate }}</span>
                        <span class="text-sm text-customGray3">％</span>
                    </div>
                    <div class="flex items-end">
                        <span class="text-sm text-customGray3 mr-1">ボトルネック満足度</span>
                        <span class="text-3xl font-semibold text-customGray3">{{ $satisfactionPercentage }}</span>
                        <span class="text-sm text-customGray3">％</span>
                    </div>
                </div>
            </div>
        <div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
        <!-- 質問項目: 繰り返し部分の例 -->
        @foreach ($questions as $index => $question)
            <div class="mb-6">
                <!-- 質問 -->
                <div class="mb-[15px]">
                    <p class="text-[16px] text-customNavy font-bold">
                        {{ $index + 1 }}. {{ $question->question }}
                    </p>
                </div>
                <!-- グラフ -->
                <div class="w-full bg-gray-200 h-[26px] overflow-hidden flex">
                    @foreach (['5' => 'customBlue', '4' => 'customLightBlue', '3' => 'customGray2', '2' => 'customLightPink2', '1' => 'customLightPink'] as $key => $color)
                        @if ($questionAnswers[$question->id][$key] > 0)
                            <div class="bg-{{ $color }} h-full flex items-center justify-center"
                                 style="width: {{ $questionAnswers[$question->id][$key] }}%;">
                                <span class="text-customNavy40 text-[11px] font-bold">
                                    {{ $questionAnswers[$question->id][$key] }}%
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <!-- 凡例 -->
                <div class="flex items-center gap-4 mt-4 transform scale-70">
                    <div class="flex items-center gap-2">
                        <div class="w-[10px] h-[10px] bg-customBlue"></div>
                        <span class="text-sm text-customGray3">している</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-[10px] h-[10px] bg-customLightBlue"></div>
                        <span class="text-sm text-customGray3">ややしている</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-[10px] h-[10px] bg-customGray2"></div>
                        <span class="text-sm text-customGray3">普通</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-[10px] h-[10px] bg-customLightPink2"></div>
                        <span class="text-sm text-customGray3">ややしていない</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-[10px] h-[10px] bg-customLightPink"></div>
                        <span class="text-sm text-customGray3">していない</span>
                    </div>
                </div>
                <div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
            </div>
        @endforeach
        </div>
    </div>

  {{-- パルスサーベイサマリー --}}
  <div>
    <div>
        <div class="max-w-[853px] mx-auto bg-white shadow-md rounded-[26px] p-6 my-8 border-5 !border-customPink">
          <!-- ヘッダ部分 -->
          <div class="flex items-center justify-between mb-4">
                <div>
                                        <div class="flex items-center mb-4">
                        <img src="{{ asset('image/BB_large.png') }}" alt="Icon" class="w-12 h-12 mr-2">
                        <h1 class="text-3xl font-bold text-customPink">
                            パルスサーベイ サマリー
                        </h1>
                    </div>
          </div>
          <div class="text-sm text-gray-500 flex items-center font-bold">
            <img src="{{ asset('image/calendar.png') }}" alt="Calendar" class="w-6 h-6 mr-2">
            〜2025/6/10
        </div>
  </div>
  <div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
                <div class="text-left my-4 p-2">
            <span class="text-[16px] font-semibold text-customGray3 block">
                森の奥深くにひとりの旅人がいた。風に乗り、彼は失われた伝説を探し続け、希望と冒険の光を胸に抱いていた。時は流れ、彼の物語は星々に語り継がれた。星降る夜、孤独な旅人は古の伝説を胸に抱き、未来への扉を静かに開いた。
            </span>
        </div>
  <div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
  <div class="w-[280px] h-[53px] bg-customLightPink rounded-[12px] flex items-center justify-center mx-auto shadow-md">
    <span class="text-[24px] font-semibold text-customWhite">
        施策の継続
    </span>
</div>
<div class="text-center text-[14px] font-bold text-customGray3 p-4">
    または
</div>
<div class="flex">
    <div class="w-[260px] h-[43px] bg-customLightPink2 rounded-[12px] flex items-center justify-center mx-auto shadow-md">
        <span class="text-[16px] font-semibold text-customWhite">
            長期目標の修正
        </span>
    </div>
    <div class="w-[260px] h-[43px] bg-customLightPink2 rounded-[12px] flex items-center justify-center mx-auto shadow-md">
        <span class="text-[16px] font-semibold text-customWhite">
            施策・目標の変更
        </span>
    </div>
</div>
</div>
</div>

{{-- パルスサーベイ結果 --}}
<div class="max-w-[853px] mx-auto bg-white shadow-md rounded-[26px] p-6 my-8 border-5 !border-customBlue">
    <div class="flex items-center justify-between mb-4">
        <div>
                                <div class="flex items-center mb-4">
                <img src="{{ asset('image/BB_large.png') }}" alt="Icon" class="w-12 h-12 mr-2">
                <h1 class="text-3xl font-bold text-customGray3">
                    施策の完了・結果
                </h1>
            </div>
  </div>
  <div class="text-sm text-gray-500 flex items-center font-bold">
    <img src="{{ asset('image/calendar.png') }}" alt="Calendar" class="w-6 h-6 mr-2">
    〜2025/6/10
</div>
</div>
<div class="w-[790px] h-[1px] bg-customGray2 mx-auto my-4"></div>
<div class="flex flex-wrap justify-between items-start gap-8">
    <!-- ボトルネックスコア -->
    <div class="flex-1 bg-white shadow-md rounded-[26px] p-6">
        <h2 class="text-customNavy text-[20px] font-bold mb-4">ボトルネックスコア</h2>
        <div class="flex items-center gap-4">
            <!-- 左側スコア -->
            <div class="text-left">
                <span class="text-[48px] font-bold text-customNavy">13.7</span>
                <span class="text-[16px] font-bold text-customNavy">/20</span>
            </div>
            <!-- 矢印 -->
            <div class="text-left">
                <span class="text-[24px] font-bold text-customLightPink">→</span>
            </div>
            <!-- 右側スコア -->
            <div class="text-left">
                <span class="text-[48px] font-bold text-customNavy">2.7</span>
                <span class="text-[16px] font-bold text-customNavy">/20</span>
            </div>
        </div>
    </div>

    <!-- 満足度 -->
    <div class="flex-1 bg-white shadow-md rounded-[26px] p-6">
        <h2 class="text-customNavy text-[20px] font-bold mb-4">満足度</h2>
        <div class="flex items-center gap-4">
            <!-- 左側スコア -->
            <div class="flex items-baseline gap-1">
                <span class="text-[48px] font-bold text-customGray3 leading-none">29</span>
                <span class="text-[24px] font-bold text-customGray3 leading-none">%</span>
            </div>
            <!-- 矢印 -->
            <div class="flex items-center justify-center">
                <span class="text-[36px] font-bold text-customNavy leading-none">▶</span>
            </div>
            <!-- 右側スコア -->
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-[96px] font-bold text-customNavy leading-none">52</span>
                <span class="text-[24px] font-bold text-customNavy leading-none">%</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>
