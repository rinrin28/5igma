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
    <div>
        <div class="bg-customLightPink text-white rounded-2xl shadow-md py-4">
            <div class="px-4">
                <p class="px-4 text-2xl font-semibold">全社主要ボトルネック</p>
            </div>
            <div class="border-b border-white mt-2"></div>
            <div class="px-6">
                <div class="flex justify-between items-start mt-4">
                    <div class="text-xl font-semibold w-3/5">
                        <p>項目{{ $bottleneck['category_id'] ?? '--' }}：</p>
                        <p>{{ $bottleneck['name'] ?? '該当なし' }}</p>
                    </div>
                    <div class="text-right font-mono">
                        <div class="text-xl">ボトルネックスコア</div>
                        <div class="text-5xl font-semibold font-mono">{{ number_format($bottleneck['score'] ?? 0, 1) }}<span class="text-2xl">/20</span></div>
                    </div>
                </div>
            </div>

            <div class="border-b border-white"></div>
            <div class="px-6">
                <div class="flex gap-8 mt-3">
                    <div class="flex w-1/2">
                        <div class="w-1/3">
                            <p class="text-lg">期待度</p>
                            <div class="text-7xl font-bold mt-4 mb-2 font-mono">{{ (int)round($latestExpectation ?? 0)}}<span class="text-4xl">%</span></div>
                            <div class="text-3xl {{ ($expectationDifference ?? 0) > 0 ? 'text-customBlue' : 'text-customPink' }}">
                                {{ ($expectationDifference ?? 0) > 0 ? '▲' : '▼' }}<span class="text-white font-semibold ml-2">{{ (int)abs($expectationDifference ?? 0) }}%</span>
                            </div>
                        </div>
                        <div class="bg-white w-2/3 rounded-xl">
                            <canvas id="expectationGraph" data-history-data='@json($historyData ?? [])'></canvas>
                        </div>
                    </div>
                    <div class="flex w-1/2">
                        <div class="w-1/3">
                            <p class="text-lg font-bold">満足度</p>
                            <div class="text-7xl font-bold mt-6 mb-2 font-mono">{{ (int)round($latestSatisfaction ?? 0)}}<span class="text-4xl">%</span></div>
                            <div class="text-3xl {{ ($satisfactionDifference ?? 0) > 0 ? 'text-customBlue' : 'text-customPink' }}">
                                {{ ($satisfactionDifference ?? 0) > 0 ? '▲' : '▼' }}<span class="text-white font-semibold ml-2">{{ (int)abs($satisfactionDifference ?? 0) }}%</span>
                            </div>
                        </div>
                        <div class="bg-white w-2/3 h-[180px] rounded-xl">
                            <canvas id="satisfactionGraph" data-history-data='@json($historyData ?? [])'></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-b border-white mt-4"></div>
            <div class="flex mt-2 px-4">
                @php
                    $groupedSubcategories = $subcategoryScoreHistory->groupBy('subcategory_question')->values();
                @endphp
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute -bottom-1 -right-1 w-[98%] h-[95%] bg-white rounded-2xl shadow-lg"></div>
                        <div class="absolute -bottom-2 -right-2 w-[96%] h-[90%] bg-white rounded-2xl shadow-lg"></div>
                        <div class="bg-white text-customNavy rounded-2xl shadow-lg px-6 py-3 relative">
                            <div class="flex justify-between items-start">
                                <div class="w-[40%]">
                                    <p class="text-lg text-customLightPink font-semibold">小項目</p>
                                    @foreach ($groupedSubcategories as $index => $data)
                                        <p class="font-semibold text-2xl mt-2 text-customLightPink subcategory-question {{ $index != 0 ? 'hidden' : '' }}" data-index="{{ $index }}">
                                            {{ $data->first()->subcategory_question }}
                                        </p>
                                    @endforeach
                                </div>
                                <div class="flex gap-8 w-[55%] px-4">
                                    <div class="w-[40%] text-center">
                                        <p class="text-2xl text-customLightPink font-bold">満足度</p>
                                        <div class="text-5xl font-semibold text-customLightPink mt-4 text-center">
                                            @foreach ($latestSubcategoryData as $index => $data)
                                                <div class="avg-score font-mono text-6xl" data-index="{{ $loop->index }}" style="display: none;">
                                                    {{ (int)round($data->avg_score) }}<span class="text-4xl font-zenkaku">%</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="w-[60%] bg-white rounded-xl border border-customGray" data-grouped-subcategories='@json($groupedSubcategories)'>
                                        @foreach ($groupedSubcategories as $index => $data)
                                            <div class="chart-container h-36" data-index="{{ $index }}" style="display: none;">
                                                <canvas class="chart-canvas" data-subcategory="{{ $data->first()->subcategory_question }}"></canvas>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="buttons-container flex flex-col gap-3 ml-6 mt-4 justify-center h-full">
                    @foreach ($groupedSubcategories as $index => $data)
                        <button class="chart-button w-7 h-7 flex items-center justify-center text-sm text-customPink bg-white rounded-full shadow-md" data-index="{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="flex gap-3">
            <div class="bg-white rounded-2xl shadow-md w-[60%]">
                <div class="flex items-center gap-2 px-4 pt-1">
                    <p class="text-customLightPink font-semibold text-base">前回のボトルネック</p>
                </div>
                <div class="border-b border-customLightPink"></div>
                <div class="px-4 py-1">
                    @if ($previousBottleneck)
                    <p class="text-sm">項目{{ $previousBottleneck['category_id'] }}：{{ $previousBottleneck['name'] }}</p>
                    @else
                        <p class="text-sm">前回のボトルネックデータは存在しません。</p>
                    @endif
                    <div class="flex">
                        <div class="bg-white text-customNavy rounded-2xl py-2 px-6 shadow-xl w-1/2 border">
                            <div class="flex items-start justify-between">
                                <p class="text-customPink text-base">ボトルネック指数</p>
                                <img src="./image/SyncAlt.png" alt="alt" class="w-5 h-5">
                            </div>
                            <div class="flex text-base text-customNavy">
                                <div class="w-3/5">
                                    <p>前回調査</p>
                                    <div class="flex">
                                        @if ($previousBottleneck)
                                            <p class="text-customBlue text-3xl font-mono">{{ round($previousBottleneck['score'], 2) }}</p>
                                        @else
                                            <p class="text-customBlue text-3xl">-</p>
                                        @endif
                                        <img src="{{ asset('image/Outbond.png') }}" alt="矢印" class="ml-6 w-8 h-8">
                                    </div>
                                </div>
                                <div class="w-2/5">
                                    <p>今回調査</p>
                                    @if ($previousBottleneck)
                                        <p class="text-customLightPink text-3xl font-mono">{{ round($previousBottleneckCurrentScore, 1)}}</p>
                                    @else
                                        <p class="text-customLightPink text-3xl">-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col w-[40%] gap-3">
                <div class="bg-customPink text-white rounded-2xl shadow-md">
                    <a href="{{ route('executive.analytics') }}" class="flex items-center px-6 py-3">
                        <img src="{{ asset('image/List.png') }}" alt="list" class="w-6 h-6">
                        <p class="text-lg font-semibold ml-4 flex-1">調査結果詳細</p>
                        <img src="{{ asset('image/ArrowIos.png') }}" alt="arrow" class="w-6 h-6">
                    </a>
                </div>
                <div class="bg-customBlue rounded-2xl px-4 py-2">
                    <p class="text-white text-lg font-semibold mb-1">ボトルネックに対する改善計画</p>
                    <div class="border-b border-white mb-3"></div>
                    <div class="bg-white rounded-2xl px-4 py-2 flex items-center gap-3">
                        <img src="{{ asset('image/timeline.png') }}" alt="timeline" class="w-6 h-6">
                        <p class="text-customNavy text-lg font-semibold flex-1">施策タイムラインへ移動</p>
                        <img src="{{ asset('image/Arrow.png') }}" alt="arrow" class="w-6 h-6">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/expectationGraph.js') }}"></script>
    <script src="{{ asset('js/satisfactionGraph.js') }}"></script>
    <script src="{{ asset('js/subCharts.js') }}"></script>
</x-app-layout> 