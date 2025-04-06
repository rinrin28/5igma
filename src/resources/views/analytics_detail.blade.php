<x-app-layout>
    <div class="bg-white  px-4 py-4 rounded-3xl">
        <div class="flex items-center">
            <img src="image/llist.png" alt="list">
            <p class="text-customNavy text-2xl font-semibold">調査結果詳細</p>
        </div>
        <p class="text-[#82868B] text-sm ml-10">質問分類項目毎の平均点や、前回の結果との比較を参照できます</p>
    </div>

    <div class="flex space-x-2 mt-8 mx-8">
        <button onclick="switchTab('main')" id="main-tab" class="px-8 py-3 text-teal-800 font-bold bg-white rounded-t-xl border-b-2 border-teal-800 focus:outline-none">
            主要項目
        </button>
        <button onclick="switchTab('sub')" id="sub-tab" class="px-8 py-3 text-gray-400 font-bold bg-white rounded-t-xl focus:outline-none">
            サブ項目
        </button>
    </div>

    <div class="min-h-screen">
        <!-- 主要項目テーブル -->
        <div id="main-content" class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold mb-6 text-customNavy">主要16項目アンケート 結果詳細</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse text-sm [border-spacing:0.5rem]">
                    <thead>
                        <tr class="text-white text-center font-bold">
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen text-start w-[40%]">主要項目</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen w-[15%]">平均スコア/期待度</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen w-[15%]">平均スコア/満足度</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen w-[15%]">前回比/期待度</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen w-[15%]">前回比/満足度</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($analyticsData as $index => $data)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-6 py-4 border-x-2 border-white text-left {{ $index % 2 == 0 ? 'bg-[#00214D33]' : 'bg-[#00214D0D]' }} text-customNavy text-base font-semibold w-[40%]">
                                {{ $index + 1 }}. {{ $data['name'] }}
                            </td>
                            <td class="px-6 py-4 border-x-2 border-white {{ $index % 2 == 0 ? 'bg-[#00214D33]' : 'bg-[#00214D0D]' }} w-[15%]">
                                <span class="text-lg">{{ number_format($data['current_expectation'], 1) }}</span>
                            </td>
                            <td class="px-6 py-4 border-x-2 border-white {{ $index % 2 == 0 ? 'bg-[#00214D33]' : 'bg-[#00214D0D]' }} w-[15%]">
                                <span class="text-lg">{{ number_format($data['current_satisfaction'], 1) }}</span>
                            </td>
                            <td class="px-6 py-4 border-x-2 border-white {{ $index % 2 == 0 ? 'bg-[#00214D33]' : 'bg-[#00214D0D]' }} w-[15%]">
                                @if($data['expectation_diff'] !== null)
                                    <span class="text-gray-600 text-lg">
                                        {{ $data['expectation_diff'] > 0 ? '+' : '' }}{{ number_format($data['expectation_diff'], 1) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 border-x-2 border-white {{ $index % 2 == 0 ? 'bg-[#00214D33]' : 'bg-[#00214D0D]' }} w-[15%]">
                                @if($data['satisfaction_diff'] !== null)
                                    <span class="text-gray-600 text-lg">
                                        {{ $data['satisfaction_diff'] > 0 ? '+' : '' }}{{ number_format($data['satisfaction_diff'], 1) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- サブ項目テーブル -->
        <div id="sub-content" class="bg-white rounded-xl shadow-md p-8 hidden">
            <h2 class="text-2xl font-bold mb-6 text-customNavy">サブ項目アンケート 結果詳細</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse text-sm [border-spacing:0.5rem]">
                    <thead>
                        <tr class="text-white text-center font-bold">
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen text-start">主要項目</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen text-start">サブ項目</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen">平均スコア/満足度</th>
                            <th class="px-6 py-4 border-x-2 border-white bg-customGreen">前回比/満足度</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subAnalyticsData as $categoryId => $subItems)
                            @foreach($subItems as $index => $item)
                                <tr class="{{ $loop->index % 2 == 0 ? 'bg-[#00214D1A]' : 'bg-[#00214D0D]' }}">
                                    @if($loop->first)
                                        <td class="px-6 py-3 border-x-2 border-white text-customNavy text-sm font-semibold align-top {{ $loop->parent->iteration % 2 == 0 ? 'bg-customGray' : 'bg-[#00214D0D]' }}" rowspan="{{ count($subItems) }}">
                                            {{ $loop->parent->iteration }}. {{ $item['category_name'] }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-3 border-x-2 border-white text-left text-customNavy text-sm font-semibold">
                                        {{ $item['description'] }}
                                    </td>
                                    <td class="px-6 py-3 border-x-2 border-white text-center">
                                        <span class="text-lg">{{ number_format($item['current_satisfaction'], 1) }}</span>
                                    </td>
                                    <td class="px-6 py-3 border-x-2 border-white text-center">
                                        @if($item['satisfaction_diff'] !== null)
                                            <span class="text-gray-600 text-lg">
                                                {{ $item['satisfaction_diff'] > 0 ? '+' : '' }}{{ number_format($item['satisfaction_diff'], 1) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // ページ読み込み時に保存されたタブの状態を復元
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('activeTab');
            if (savedTab) {
                switchTab(savedTab);
            }
        });

        function switchTab(tab) {
            const mainTab = document.getElementById('main-tab');
            const subTab = document.getElementById('sub-tab');
            const mainContent = document.getElementById('main-content');
            const subContent = document.getElementById('sub-content');

            if (tab === 'main') {
                mainTab.classList.add('text-teal-800', 'border-b-2', 'border-teal-800');
                mainTab.classList.remove('text-gray-400');
                subTab.classList.remove('text-teal-800', 'border-b-2', 'border-teal-800');
                subTab.classList.add('text-gray-400');
                mainContent.classList.remove('hidden');
                subContent.classList.add('hidden');
            } else {
                subTab.classList.add('text-teal-800', 'border-b-2', 'border-teal-800');
                subTab.classList.remove('text-gray-400');
                mainTab.classList.remove('text-teal-800', 'border-b-2', 'border-teal-800');
                mainTab.classList.add('text-gray-400');
                subContent.classList.remove('hidden');
                mainContent.classList.add('hidden');
            }

            // タブの状態をローカルストレージに保存
            localStorage.setItem('activeTab', tab);
        }
    </script>
</x-app-layout>