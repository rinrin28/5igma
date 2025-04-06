@php
    // Controller から渡された「主要ボトルネック」を直接利用
    $categoryId      = $bottleneck['category_id'] ?? null;
    $bottleneckTitle = $bottleneckTitle ?? ($bottleneck['title'] ?? '該当なし');
    $bottleneckScore = isset($bottleneckScore)
        ? number_format($bottleneckScore, 2)
        : null;

    // AIService から返却された JSON から「recommendations」だけ取り出す
    $recommendations = [];
    if (!empty($aiRecommendations)) {
        $decoded = json_decode($aiRecommendations, true);
        $recommendations = $decoded ['recommendations'] ?? [];
    }
@endphp

<x-app-layout>
    <div class="flex flex-col h-full w-full overflow-hidden">
        <!-- 上部セクション -->
        <section class="flex w-full">
            <!-- 左側のパネル -->
            <section class="w-80">
                <!-- 調査実施日 -->
                <section>
                    <div class="bg-white rounded-3xl px-8 py-1.5 shadow-xl text-customPink w-80">
                        <div class="text-sm font-semibold my-0.5">全社調査実施日</div>
                        @if($currentSurvey)
                            <div class="text-xs font-mono font-semibold">
                                {{ $currentSurvey->start_date->format('Y') }}
                            </div>
                            <div class="text-3xl font-bold font-mono mt-0.5 tracking-wide">
                                {{ $currentSurvey->start_date->format('n/j') }}
                                <span class="text-2xl mx-4 font-mono">-</span>
                                {{ $currentSurvey->end_date->format('n/j') }}
                            </div>
                        @else
                            <div class="text-sm text-gray-500 mt-2">調査データがありません</div>
                        @endif
                    </div>

                    <!-- 回答状況 -->
                    <div class="bg-customLightPink rounded-3xl px-8 py-1.5 shadow-xl text-white flex justify-around items-center mt-2 mb-2">
                        <div class="text-center">
                            <div class="text-sm font-semibold my-0.5">有効回答数</div>
                            <div class="flex items-end justify-center">
                                <span class="text-3xl font-mono font-bold leading-none">{{ $validCount }}</span>
                                <span class="text-xs font-mono font-semibold ml-1">/ {{ $totalCount }}</span>
                                <span class="font-zenkaku font-semibold text-sm">件</span>
                            </div>
                        </div>
                        <div class="text-light">
                            <div class="text-sm font-semibold my-0.5">回答率</div>
                            <div class="flex items-end justify-center">
                                <span class="text-3xl font-mono font-bold leading-none">{{ round($responseRate) }}</span>
                                <span class="text-xs ml-1">%</span>
                                <span class="flex items-center text-xs ml-5 mb-1">
                                    @if($deltaRate >= 0)
                                        <span class="text-green-300">▲</span>{{ abs(round($deltaRate)) }}%
                                    @else
                                        <span class="text-red-300">▼</span>{{ abs(round($deltaRate)) }}%
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- レーダーチャート -->
                <section class="bg-white rounded-3xl p-3 shadow-xl">
                    <p class="font-zenkaku font-bold mx-2">全社主要16項目・満足度</p>
                    <div style="height: 275px;">
                        <canvas id="radarChart" 
                            data-previous='@json($previousData ?? array_fill(0, 16, 0))' 
                            data-current='@json($currentData ?? array_fill(0, 16, 0))'>
                        </canvas>
                    </div>
                </section>
            </section>

            <!-- 右側のパネル -->
            <section class="flex-1 px-8 py-3 bg-customLightPink rounded-3xl text-white ml-4 shadow-xl">
                <!-- 満足度スコア -->
                <div class="my-1 flex h-1/2">
                    <div>
                        <h2 class="text-3xl font-bold font-zenkaku">全社主要<span class="font-mono">16</span>項目・満足度</h2>
                        <div class="text-7xl font-bold flex items-center mt-8">
                            <span id="latestScore">--</span><span>%</span>
                            <span id="trendIcon" class="ml-auto text-4xl text-customBlue">▲</span>
                            <span id="scoreDifference" class="text-4xl ml-3">--</span>
                        </div>
                    </div>
                    <div class="ml-auto w-3/5 h-40">
                        <canvas id="myChart" 
                            class="bg-white rounded-lg shadow-lg"
                            data-latest-score='@json($latestScore)'
                            data-score-difference='@json($scoreDifference)'
                            data-satisfaction-data='@json($satisfactionData)'>
                        </canvas>
                    </div>
                </div>

                <!-- ボトルネックスコア -->
                <div class="my-1 flex h-1/2">
                    <div>
                        <div class="flex">
                            <h2 class="text-3xl font-bold font-zenkaku">全社期待度 × 満足度</h2>
                            <button class="ml-4 text-white bg-white bg-opacity-30 w-8 h-8 rounded-full border border-white text-lg">?</button>
                        </div>
                        <h2 class="text-3xl font-bold font-zenkaku">ボトルネックスコア</h2>
                        <div class="text-7xl font-bold flex items-center mt-6">
                            <span id="bnLatestScore">--</span>
                            <span id="bnTrendIcon" class="ml-auto text-4xl text-customYellow">▲</span>
                            <span id="bnDifference" class="text-4xl ml-3">--</span>
                        </div>
                    </div>
                    <div class="ml-auto w-3/5 h-40">
                        <canvas id="mChart" 
                            class="bg-white rounded-lg shadow-lg"
                            data-latest-score='@json($latestBottleneckScore)'
                            data-score-difference='@json($bottleneckDifference)'
                            data-bottleneck-data='@json($bottleneckData)'>
                        </canvas>
                    </div>
                </div>
            </section>
        </section>

        <!-- 下部セクション -->
        <section class="flex w-full mt-2">
            <!-- マトリクス -->
            <section class="w-1/2 rounded-3xl p-4 bg-white shadow-xl">
                <div class="flex">
                    <div class="text-customLightPink text-2xl font-zenkaku font-bold">
                        全社期待度×満足度<br>マトリクス
                    </div>
                    <div class="ml-10" style="height: 200px; width: 350px;">
                        <canvas id="bubbleChart" data-matrix='@json($matrixData)' class="bg-white rounded-lg shadow-lg px-2"></canvas>
                    </div>
                </div>
            </section>

            <!-- ステータスパネル -->
            <section class="flex flex-col w-1/2 ml-3 space-y-2">
                <!-- 満足度ステータス -->
                <div class="h-1/3 shadow-xl rounded-3xl bg-customBlue bg-opacity-50 py-1.5">
                    <p class="text-teal-700 font-bold flex items-center px-6">
                        ○ 全社主要16項目・満足度
                    </p>
                    <div class="border-t border-teal-600 mt-0.5 mb-1"></div>
                    <p class="text-teal-700 mt-0.5 font-bold px-6">改善傾向です</p>
                </div>

                <!-- ボトルネックステータス -->
                <div class="h-1/3 shadow-xl rounded-3xl bg-customYellow bg-opacity-50 py-1.5">
                    <p class="text-yellow-600 font-bold flex items-center px-6">
                        △ 全社期待度×満足度ボトルネックスコア
                    </p>
                    <div class="border-t border-yellow-500 mt-0.5 mb-1"></div>
                    <p class="text-yellow-600 mt-0.5 font-bold px-6">要注意です</p>
                </div>

                <!-- ボトルネック詳細 -->
                <div class="h-1/3 shadow-xl rounded-3xl bg-white py-1.5">
                    <p class="text-customPink font-bold px-6">
                        【全社主要ボトルネック】期待度×満足度マトリクス
                    </p>
                    <div class="border-t border-gray-300 mt-0.5 mb-1"></div>
                    <div class="flex px-6">
                        <div>
                            @if ($bottleneck)
                                <p class="text-customLightPink mt-0.5 font-bold">
                                    {{ $bottleneck['label'] }}：{{ $bottleneck['name'] }}
                                </p>
                            @else
                                <p>ボトルネックはありません</p>
                            @endif
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('executive.analytics_detail') }}" class="flex items-center justify-center hover:bg-opacity-80 transition">
                                <svg class="w-10 h-8 ml-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10l12 14-12 14" />
                                </svg>
                                <svg class="w-10 h-8 -ml-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10l12 14-12 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </section>

    </div>

    <!-- チャートスクリプト -->
    <script src="{{ asset('js/bubbleChart.js') }}"></script>
    <script src="{{ asset('js/radarChart.js') }}"></script>
    <script src="{{ asset('js/lineGraph.js') }}"></script>
    <script src="{{ asset('js/bnLineGraph.js') }}"></script>
</x-app-layout> 