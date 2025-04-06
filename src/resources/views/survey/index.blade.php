<!DOCTYPE html>
    <html lang="ja">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>職場環境改善アンケート</title>
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
        /* 矢印のスタイル */
        .arrow-up {
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 12px solid #00214D;
        }
        
        .arrow-down {
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 12px solid #00214D;
        }
        .category-number {
            border: 4px solid transparent;
            transition: all 0.3s ease;
        }
        .category-number.active {
            border-color: #00FFFF;
            background-color: white !important;
            color: #00214D !important;
        }
        .category-number.completed {
            border-color: #00FFFF;
            background-color: white !important;
            color: #00214D !important;
        }
        .satisfaction-circle {
            border-color: #82868B;
        }
        .satisfaction-circle.selected {
            border-color: #FF768D;
            background-color: #FF768D;
            color: white;
        }
        @media (max-width: 1024px) {
                .header{
                    width: 100vw;
                }
                .company{
                    font-size: 15px;
                    white-space: nowrap;
                }
                .span{
                    display: none;
                    white-space: nowrap;
                }
                .name{
                    font-size: 15px;
                }
                .title{
                    font-size: 15px;
                    white-space: nowrap;
                }
                .background{
                    display: none;
                }
                .background2{
                    width: 100vw;
                }
                .menu{
                    display: none;
                }
                .flex-1{
                    left: 0px;
                }
                .header {
                    width: 100vw;
                    height: auto;
                    padding: 8px 16px;
                    }

                    .header > div {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 4px;
                    }

                    .title {
                        font-size: 16px;
                        white-space: nowrap;
                    }

                    .company {
                        font-size: 14px;
                        white-space: nowrap;
                    }

                    .span {
                        display: none !important;
                    }

                    .name {
                        font-size: 14px;
                        white-space: nowrap;
                    }

                    .header .span:last-child {
                        position: absolute;
                        top: 8px;
                        right: 12px;
                        display: flex !important;
                        gap: 4px;
                    }

                    .flex-1 {
                        left: 0 !important;
                    }
                    .question{
                        white-space: nowrap !important;
                        width: 100% !important; 
                        border-radius: 50px !important;
                        height: 200px !important;
                        margin-top: 50px;
                    }
                    .background2 {
                        width: 100vw !important;
                        border-radius: 0 !important;
                        margin-top: 0;
                    }
                    .background2 > .h-full > div {
                        padding-left: 1rem !important;
                        padding-right: 1rem !important; 
                        padding-top: 2rem !important;
                        padding-bottom: 2rem !important;
                    }
                    .category-group > div {
                        flex-direction: column !important;
                        height: auto !important;
                        padding: 1.5rem !important;
                    }
                    .expectation-block,
                    .satisfaction-block {
                        width: 100% !important;
                        margin-bottom: 1.5rem;
                    }
                    .category-group > div > .text-center {
                        width: 100% !important;
                        margin: 1rem 0 !important;
                    }
                    .satisfaction-circle {
                        width: 60px !important;
                        height: 60px !important;
                        font-size: 0.875rem !important; 
                    }
                    .text-center {
                        width: 100% !important;
                        margin: 1rem 0 !important;
                    }
                    .category-group .w-\[450px\] {
                        gap: 0.2rem !important;
                        
                    }
                    .choice2{
                        white-space: nowrap;
                    }
                    .category-group .w-\[450px\] {
                        display: flex !important;
                        justify-content: flex-start !important;
                        width: auto !important;
                        max-width: 100% !important;
                        padding-left: -0.5rem !important;
                        gap: 0.3rem !important; 
                        align-items: flex-start !important;
                        justify-content: center;
                    }
                    .category-group .w-\[450px\] .satisfaction-circle {
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                        flex-shrink: 0 !important;
                    }
                    .category-group .ml-4,
                    .category-group .mr-4 {
                        margin-left: 0.25rem !important;
                        margin-right: 0.25rem !important;
                    }
                    .choice2 {
                        white-space: nowrap;
                        font-size: 0.75rem !important;
                        text-align: center;
                    }
                    .choicebox{
                        margin-left: -60px;
                    }
                    .responsive-br {
                        display: inline !important;
                    }
                    .expectation-block .satisfaction-circle[data-value="1"] {
                        top: 60px;
                        left: -40px;
                        width: 60px;
                        height: 60px;
                    }

                    .expectation-block .satisfaction-circle[data-value="2"] {
                        top: 40px;
                        left: 20px;
                        width: 70px;
                        height: 70px;
                    }

                    .expectation-block .satisfaction-circle[data-value="3"] {
                        top: 20px;
                        left: 80px;
                        width: 80px;
                        height: 80px;
                    }

                    .expectation-block .satisfaction-circle[data-value="4"] {
                        top: 40px;
                        left: 140px;
                        width: 70px;
                        height: 70px;
                    }

                    .expectation-block .satisfaction-circle[data-value="5"] {
                        top: 60px;
                        left: 200px;
                        width: 60px;
                        height: 60px;
                    }
                    .expectation-block{
                        display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 100%;              
                    overflow: visible;       
                    position: absolute;
                    margin-top: -180px;
                    margin-right: 95px;
                    }
                    .parent-clock{
                        position: relative;
                    }

                    .satisfaction-block .satisfaction-circle[data-value="1"] {
                    top: -20px;
                    left: -40px;
                    width: 60px;
                    height: 60px;
                    }

                    .satisfaction-block .satisfaction-circle[data-value="2"] {
                    top: 0px;
                    left: 20px;
                    width: 70px;
                    height: 70px;
                    }

                    .satisfaction-block .satisfaction-circle[data-value="3"] {
                    top: 20px;
                    left: 80px;
                    width: 80px;
                    height: 80px;
                    }

                    .satisfaction-block .satisfaction-circle[data-value="4"] {
                    top: 0px;
                    left: 140px;
                    width: 70px;
                    height: 70px;
                    }

                    .satisfaction-block .satisfaction-circle[data-value="5"] {
                    top: -20px;
                    left: 200px;
                    width: 60px;
                    height: 60px;
                    }

                    .satisfaction-block{
                        display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 100%;             
                    overflow: visible;        
                    position: absolute;
                    margin-top: 540px;
                    margin-left: -110px;
                    }
                    .category-group > div.mb-16 {
                        height: 450px !important;
                    }
                    .yesno{
                        position: relative;
                    }
                    .yes{
                        position: absolute;
                        left: 230px;
                        top: 340px;
                    }
                    .no{
                        position: absolute;
                        left: 0px;
                        top: 335px;
                    }
                    .satisfactionp{
                        position: relative;
                    }
                    .satisfactionc{
                        position: absolute;
                        left: 99px;
                        bottom: 155px;
                        font-size: 15px;
                    }
                    .expectionp{
                        position: relative;
                    }
                    .expectionc{
                        position: absolute;
                        left: 67px;
                        top: 90px;
                        font-size: 15px;
                    }
                    .before{
                        margin-bottom: 10px;
                    }
                    .save{
                        left: 250px !important;
                    }
                                }
    </style>
    </head>
    <body class="font-mono">
    <!-- ヘッダー -->
    <header class="header w-full h-[60px] bg-[#E8EBF0] shadow-sm rounded-br-3xl rounded-bl-3xl fixed top-0 left-0 z-[80]">
        <div class="max-w-screen-xl mx-auto px-6 h-full flex items-center justify-between">
            <h1 class="title text-2xl font-bold text-customPink">職場環境改善アンケート</h1>
            <div class="flex items-center space-x-2 text-customNavy">
                <span class="company text-lg">金堂印刷株式会社</span>
                <span class="span text-customNavy">/</span>
                <span class="span text-sm">{{ $department->name ?? '部署なし' }}</span>
                <span class="name text-customNavy ml-4">{{ $user->name ?? 'ユーザー不明' }}</span>
                <span class="name text-customNavy">さん</span>
            </div>
            <div class="span flex items-center space-x-2">
                <span class="text-lg font-semibold text-customNavy">{{ $survey->year ?? date('Y') }}</span>
                <span class="text-sm text-customNavy">{{ $survey->period ?? '上半期' }}</span>
            </div>
        </div>
    </header>

    <!-- 保存ステータス -->
    <div id="saveStatus" class="save fixed top-[120px] left-[340px] py-2 px-4 rounded-full bg-white shadow-sm text-sm hidden z-[90]">
        <span class="flex items-center">
            <img src="{{ asset('image/store.png') }}" alt="check" class="w-4 h-4 mr-2">
            保存済み
        </span>
    </div>

    <main class="flex">
        <!-- 背景要素を分離 -->
        <div class="background bg-[#DADEE5] rounded-tr-3xl rounded-br-3xl min-w-[300px] fixed top-[80px] left-0 bottom-0 z-[30]"></div>

        <!-- 左側のカテゴリーメニュー -->
        <aside class="menu min-w-[300px] fixed top-[80px] left-0 bottom-0 flex items-center pl-6 py-28 z-[40]">
            <div class="flex flex-col justify-between h-full w-full">
                <div class="flex items-center justify-between relative">
                    <span class="text-xl text-[#00214D] leading-relaxed w-[190px] font-bold">
                        経営・戦略<br>に関する項目
                    </span>
                    <div class="category-number active w-[90px] h-[90px] rounded-full flex items-center justify-center text-[1.75rem] font-normal bg-white text-[#82868B] absolute -right-[35px]" data-group="1">
                        1
                    </div>
                </div>
                <div class="flex items-center justify-between relative">
                    <span class="text-xl text-[#00214D] leading-relaxed w-[190px] font-bold">
                        組織文化・人間関係<br>に関する項目
                    </span>
                    <div class="category-number w-[90px] h-[90px] rounded-full flex items-center justify-center text-[1.75rem] font-normal bg-white text-[#82868B] absolute -right-[35px]" data-group="2">
                        2
                    </div>
                </div>
                <div class="flex items-center justify-between relative">
                    <span class="text-xl text-[#00214D] leading-relaxed w-[190px] font-bold">
                        働く環境・制度<br>に関する項目
                    </span>
                    <div class="category-number w-[90px] h-[90px] rounded-full flex items-center justify-center text-[1.75rem] font-normal bg-white text-[#82868B] absolute -right-[35px]" data-group="3">
                        3
                    </div>
                </div>
                <div class="flex items-center justify-between relative">
                    <span class="text-xl text-[#00214D] leading-relaxed w-[220px] font-bold">
                        コミュニケーション・教育<br>に関する項目
                    </span>
                    <div class="category-number w-[90px] h-[90px] rounded-full flex items-center justify-center text-[1.75rem] font-normal bg-white text-[#82868B] absolute -right-[35px]" data-group="4">
                        4
                    </div>
                </div>
                <div class="flex items-center justify-between relative">
                    <span class="text-xl text-[#00214D] leading-relaxed w-[190px] font-bold">
                        業務の質・成果<br>に関する項目
                    </span>
                    <div class="category-number w-[90px] h-[90px] rounded-full flex items-center justify-center text-[1.75rem] font-normal bg-white text-[#82868B] absolute -right-[35px]" data-group="5">
                        5
                    </div>
                </div>
            </div>
        </aside>

        <!-- メインコンテンツ -->
        <div class="flex-1 fixed top-[50px] left-[280px] right-0 bottom-0 z-[35]">
            <div class="background2 bg-[#F5F6F8] mt-12 rounded-xl h-[calc(100vh-100px)]">
                <div class="h-full overflow-y-auto">
                    <div class="py-12 pl-24 pr-8">
                        <div class="background3 h-[40px] bg-[#F5F6F8] sticky top-0 z-10"></div>
                        <form id="surveyForm" action="{{ route('survey.store') }}" method="POST" class="space-y-12" 
                            data-total-categories="{{ count($categories) }}"
                            data-total-subcategories="{{ count($categories->flatMap->subcategories) }}">
                @csrf
                            @foreach($categories as $categoryIndex => $category)
                            @php
                                $groupNumber = 1;
                                if ($categoryIndex >= 5 && $categoryIndex < 8) {
                                    $groupNumber = 2;
                                } elseif ($categoryIndex >= 8 && $categoryIndex < 11) {
                                    $groupNumber = 3;
                                } elseif ($categoryIndex >= 11 && $categoryIndex < 14) {
                                    $groupNumber = 4;
                                } elseif ($categoryIndex >= 14) {
                                    $groupNumber = 5;
                                }
                            @endphp
                            <div class="category-group {{ $categoryIndex === 0 ? 'block' : 'hidden' }}" data-group="{{ $groupNumber }}" data-category-id="{{ $category->id }}" data-index="{{ $categoryIndex }}">
                                <!-- 期待度と満足度 -->
                                <div class="mb-16 bg-white rounded-xl p-4 h-[450px] shadow-sm flex items-center">
                                    <div class="parent-clock flex items-center justify-center w-full">
                                        <!-- 期待度ブロック -->
                                        <div class="expectation-block flex flex-col items-center w-1/4">
                                            <div class="expectionp flex items-center space-x-4">
                                                <div class="relative h-40 w-32">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $positions = [
                                                                1 => '-bottom-32 left-40 w-24 h-24',
                                                                2 => '-bottom-10 left-24 w-20 h-20',
                                                                3 => 'bottom-12 left-10 w-16 h-16',
                                                                4 => 'bottom-32 left-20 w-20 h-20',
                                                                5 => 'bottom-48 left-40 w-24 h-24'
                                                            ];
                                                        @endphp
                                                        <div class="satisfaction-circle absolute rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 {{ $positions[$i] }} flex items-center justify-center text-[#82868B]"
                                                            data-value="{{ $i }}"
                                                            data-category-id="{{ $category->id }}"
                                                            onclick="selectExpectation(this)">
                                                        </div>
                                                    @endfor
                                                </div>
                                                <span class="expectionc text-lg font-bold">期待度</span>
                                            </div>
                                            <input type="hidden" name="expectations[{{ $category->id }}]" id="expectation_{{ $category->id }}">
                                        </div>

                                        <!-- 大項目の設問 -->
                                        <div class="text-center w-2/4 mx-12 mb-6">
                                            <div class="yesno flex flex-col items-center mt-8">
                                                <span class="yes font-bold mb-2">している</span>
                                                <div class="span arrow-up"></div>
                                                <div class="span w-[1px] h-16 bg-[#00214D]"></div>
                                                <div class="question bg-[#DADEE5] bg-opacity-20 rounded-full px-16 py-8">
                                                    <h2 class="text-xl font-bold mb-4">《{{ $category->name }}》</h2>
                                                    <p class="text-gray-700 whitespace-pre-wrap text-center">{{ $category->question }}</p>
                                                </div>
                                                <div class="span w-[1px] h-16 bg-[#00214D]"></div>
                                                <div class="span arrow-down"></div>
                                                <span class="no font-bold mt-2">していない</span>
                                            </div>
                                        </div>

                                        <!-- 満足度ブロック -->
                                        <div class="satisfaction-block flex flex-col items-center w-1/4">
                                            <div class="satisfactionp flex items-center justify-end space-x-4">
                                                <span class="satisfactionc text-lg font-bold">満足度</span>
                                                <div class="relative h-40 w-32">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $positions = [
                                                                1 => '-bottom-32 right-40 w-24 h-24',
                                                                2 => '-bottom-10 right-24 w-20 h-20',
                                                                3 => 'bottom-12 right-10 w-16 h-16',
                                                                4 => 'bottom-32 right-20 w-20 h-20',
                                                                5 => 'bottom-48 right-40 w-24 h-24'
                                                            ];
                                                        @endphp
                                                        <div class="satisfaction-circle absolute rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 {{ $positions[$i] }} flex items-center justify-center text-[#82868B]"
                                                            data-value="{{ $i }}"
                                                            data-category-id="{{ $category->id }}"
                                                            onclick="selectSatisfaction(this)">
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                            <input type="hidden" name="satisfactions[{{ $category->id }}]" id="satisfaction_{{ $category->id }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- サブカテゴリーの質問 -->
                                @foreach($category->subcategories as $subcategory)
                                <div class="mb-8 bg-white rounded-xl p-8 shadow-sm">
                                    <div class="flex flex-col items-center">
                                        <h3 class="text-xl font-bold mb-4 text-center">
                                            《{{ $subcategory->name }}》
                                            <span class="responsive-br hidden lg:inline"><br></span>
                                            <span class="ml-4">{{ $subcategory->question }}</span>
                                        </h3>
                                        <div class="choicebox flex flex-col items-center w-full max-w-4xl mt-4">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex flex-col items-center ml-4">
                                                    <div class="satisfaction-circle w-24 h-24 rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 flex items-center justify-center text-[#82868B] text-lg"
                                                        data-value="1"
                                                        data-subcategory-id="{{ $subcategory->id }}"
                                                    >
                                                        
                                                    </div>
                                                    <span class="choice2 font-bold mt-4">満足していない</span>
                                                </div>
                                                <div class="flex items-center justify-between w-[450px] mb-6">
                                                    @for($i = 2; $i <= 4; $i++)
                                                    @php
                                                        $sizes = [
                                                            2 => 'w-20 h-20',
                                                            3 => 'w-16 h-16',
                                                            4 => 'w-20 h-20'
                                                        ];
                                                    @endphp
                                                    <div class="satisfaction-circle {{ $sizes[$i] }} rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 flex items-center justify-center text-[#82868B] text-lg"
                                                        data-value="{{ $i }}"
                                                        data-subcategory-id="{{ $subcategory->id }}"
                                                    >
                                                    
                                                    </div>
                                                    @endfor
                                                </div>
                                                <div class="flex flex-col items-center mr-4">
                                                    <div class="satisfaction-circle w-24 h-24 rounded-full border-8 cursor-pointer transition-all duration-300 hover:scale-110 flex items-center justify-center text-[#82868B] text-lg"
                                                        data-value="5"
                                                        data-subcategory-id="{{ $subcategory->id }}"
                                                    >
                                                        
                                                    </div>
                                                    <span class="choice2 font-bold mt-4">満足している</span>
                                                </div>
                                            </div>
                            </div>
                                        <input type="hidden" name="sub_responses[{{ $subcategory->id }}]" id="sub_response_{{ $subcategory->id }}">
                            </div>
                        </div>
                                @endforeach

                                <!-- ナビゲーションボタン -->
                                <div class="flex justify-center mt-8 space-x-4">
                                    @if($categoryIndex > 0)
                                        <button type="button" 
                                            class="before bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200 prev-button"
                                            data-category-id="{{ $category->id }}"
                                        >前へ</button>
                                    @endif

                                    @if($categoryIndex === count($categories) - 1)
                                        <button type="submit" 
                                            class="bg-customPink text-white px-8 py-2 rounded-lg hover:bg-pink-600 transition-colors duration-200"
                                        >送信</button>
                                    @else
                                        <button type="button" 
                                            class="bg-customPink text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition-colors duration-200 next-button"
                                            data-category-id="{{ $category->id }}"
                                        >
                                            @if($categoryIndex === 4 || $categoryIndex === 7 || $categoryIndex === 10 || $categoryIndex === 13)
                                                次のセクションへ
                                            @else
                                                次へ
                                            @endif
                                        </button>
                                    @endif
                                </div>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let expectations = {};
        let satisfactions = {};
        let subResponses = {};
        let currentGroup = 1;
        let currentCategoryIndex = 0;
        let completedGroups = new Set();
        let saveTimeout = null;
        let isSaving = false;

        const form = document.getElementById('surveyForm');
        const totalCategories = parseInt(form.dataset.totalCategories);
        const totalSubcategories = parseInt(form.dataset.totalSubcategories);

        // ページ読み込み時にイベントリスナーを設定
        document.addEventListener('DOMContentLoaded', function() {
            // ローカルストレージから保存された回答を復元
            const savedExpectations = localStorage.getItem('expectations');
            const savedSatisfactions = localStorage.getItem('satisfactions');
            const savedSubResponses = localStorage.getItem('subResponses');
            const savedCurrentGroup = localStorage.getItem('currentGroup');
            const savedCurrentCategoryIndex = localStorage.getItem('currentCategoryIndex');
            const savedScrollPosition = localStorage.getItem('scrollPosition');
            
            // 保存されたデータがある場合は復元
            if (savedExpectations) {
                expectations = JSON.parse(savedExpectations);
                Object.entries(expectations).forEach(([categoryId, value]) => {
                    // 期待度の値を設定
                    const input = document.getElementById(`expectation_${categoryId}`);
                    if (input) {
                        input.value = value;
                        // 対応する円を選択状態にする
                        const circle = document.querySelector(`.expectation-block .satisfaction-circle[data-value="${value}"][data-category-id="${categoryId}"]`);
                        if (circle) {
                            circle.classList.add('selected');
                        }
                    }
                });
            }

            if (savedSatisfactions) {
                satisfactions = JSON.parse(savedSatisfactions);
                Object.entries(satisfactions).forEach(([categoryId, value]) => {
                    // 満足度の値を設定
                    const input = document.getElementById(`satisfaction_${categoryId}`);
                    if (input) {
                        input.value = value;
                        // 対応する円を選択状態にする
                        const circle = document.querySelector(`.satisfaction-block .satisfaction-circle[data-value="${value}"][data-category-id="${categoryId}"]`);
                        if (circle) {
                            circle.classList.add('selected');
                        }
                    }
                });
            }

            if (savedSubResponses) {
                subResponses = JSON.parse(savedSubResponses);
                Object.entries(subResponses).forEach(([subcategoryId, value]) => {
                    // サブカテゴリーの値を設定
                    const input = document.getElementById(`sub_response_${subcategoryId}`);
                    if (input) {
                        input.value = value;
                        // 対応する円を選択状態にする
                        const circle = document.querySelector(`.satisfaction-circle[data-value="${value}"][data-subcategory-id="${subcategoryId}"]`);
                        if (circle) {
                            circle.classList.add('selected');
                        }
                    }
                });
            }

            // 現在のグループとカテゴリーインデックスを復元
            if (savedCurrentGroup) {
                currentGroup = parseInt(savedCurrentGroup);
            }

            if (savedCurrentCategoryIndex) {
                currentCategoryIndex = parseInt(savedCurrentCategoryIndex);
                // 対応するカテゴリーを表示
                document.querySelectorAll('.category-group').forEach((group, index) => {
                    if (index === currentCategoryIndex) {
                        group.classList.remove('hidden');
                        group.classList.add('block');
                    } else {
                        group.classList.add('hidden');
                        group.classList.remove('block');
                    }
                });
            }

            // カテゴリー番号を更新
            updateCategoryNumbers();

            // スクロール位置を復元
            if (savedScrollPosition) {
                const scrollContainer = document.querySelector('.overflow-y-auto');
                if (scrollContainer) {
                    setTimeout(() => {
                        scrollContainer.scrollTop = parseInt(savedScrollPosition);
                    }, 100);
                }
            }

            // 期待度と満足度の両方が回答済みの場合に、サブカテゴリーにスクロール
            setTimeout(() => {
                const currentGroup = document.querySelector('.category-group:not(.hidden)');
                if (currentGroup) {
                    scrollToSubcategoriesIfNeeded(currentGroup);
                }
            }, 1500);

            // 期待度と満足度の円のイベントリスナー
            document.querySelectorAll('.satisfaction-circle').forEach(circle => {
                circle.addEventListener('click', function() {
                    console.log('Clicked circle:', this.dataset.value);
                    const categoryGroup = this.closest('.category-group');
                    const categoryId = categoryGroup?.dataset.categoryId;
                    const value = parseInt(this.dataset.value);
                    
                    if (this.closest('.expectation-block')) {
                        selectExpectation(this);
                    } else if (this.closest('.satisfaction-block')) {
                        selectSatisfaction(this);
                    } else if (this.dataset.subcategoryId) {
                        // サブカテゴリーの円の場合
                        const subcategoryId = parseInt(this.dataset.subcategoryId);
                        selectSubResponse(this, subcategoryId, value);
                    }

                    // グループの完了状態をチェック
                    checkGroupCompletion();
                });
            });

            // 前へ/次へボタンのイベントリスナー
            document.querySelectorAll('.prev-button').forEach(button => {
                button.addEventListener('click', function() {
                    showPreviousCategory(this.dataset.categoryId);
                });
            });

            document.querySelectorAll('.next-button').forEach(button => {
                button.addEventListener('click', function() {
                    showNextCategory(this.dataset.categoryId);
                });
            });

            // フォームの送信イベントをハンドル
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // フォームのデフォルトの送信を防止
                
                // 全てのカテゴリーの回答をチェック
                const allCategories = document.querySelectorAll('.category-group');
                let hasUnanswered = false;
                let firstUnansweredCategory = null;
                
                allCategories.forEach((category) => {
                    // カテゴリーを表示状態にする（一時的に）
                    const wasHidden = category.classList.contains('hidden');
                    if (wasHidden) {
                        category.classList.remove('hidden');
                        category.classList.add('block');
                    }
                    
                    // 回答チェック
                    if (!checkCategoryCompletion(category)) {
                        hasUnanswered = true;
                        if (!firstUnansweredCategory) {
                            firstUnansweredCategory = category;
                        }
                    }
                    
                    // カテゴリーを元の状態に戻す
                    if (wasHidden) {
                        category.classList.add('hidden');
                        category.classList.remove('block');
                    }
                });
                
                if (hasUnanswered) {
                    alert('未回答の項目があります。全ての項目に回答してください。');
                    
                    // 最初の未回答カテゴリーを表示と処理
                    if (firstUnansweredCategory) {
                        showUnansweredCategory(firstUnansweredCategory, allCategories);
                    }
                    return;
                }
                
                // 全て回答済みの場合はフォームを送信
                console.log('全ての回答が完了しています。フォームを送信します。');
                
                // ローカルストレージをクリア
                localStorage.removeItem('expectations');
                localStorage.removeItem('satisfactions');
                localStorage.removeItem('subResponses');
                localStorage.removeItem('currentGroup');
                localStorage.removeItem('currentCategoryIndex');
                localStorage.removeItem('scrollPosition');
                
                // 通常のフォーム送信を行う
                this.submit();
            });

            document.querySelectorAll('.satisfaction-circle[data-subcategory-id]').forEach(circle => {
                circle.addEventListener('click', function() {
                    const value = this.dataset.value;
                    const subcategoryId = this.dataset.subcategoryId;
                    selectSubResponse(this, subcategoryId, parseInt(value));
                });
            });
        });

        function selectExpectation(element) {
            const categoryId = element.dataset.categoryId;
            const value = parseInt(element.dataset.value);
            
            // 同じカテゴリー内の他の期待度の円から選択を解除
            const categoryGroup = element.closest('.category-group');
            categoryGroup.querySelectorAll('.expectation-block .satisfaction-circle').forEach(circle => {
                circle.classList.remove('selected');
            });
            
            // クリックされた円を選択状態にする
            element.classList.add('selected');
            
            // 隠しフィールドに値を設定
            document.getElementById(`expectation_${categoryId}`).value = value;
            
            // デバッグ用のログ
            console.log(`期待度を設定: カテゴリー${categoryId}, 値${value}`);
            
            // 進捗を保存
            saveProgress();
            
            // 満足度が既に回答済みかチェック
            const satisfactionSelected = categoryGroup.querySelector('.satisfaction-block .satisfaction-circle.selected');
            
            if (satisfactionSelected) {
                // 満足度が回答済みの場合は、サブカテゴリーにスクロール
                console.log('期待度と満足度の両方が回答済みです。サブカテゴリーにスクロールします。');
                const subcategoryGroups = categoryGroup.querySelectorAll('.mb-8');
                if (subcategoryGroups.length > 0) {
                    // 最初のサブカテゴリーグループにスクロール（遅延を長めに設定）
                    setTimeout(() => {
                        scrollToElementCenter(subcategoryGroups[0]);
                    }, 800);
                }
            } else {
                // 満足度が未回答の場合は、満足度にスクロール
                const unansweredSatisfaction = categoryGroup.querySelector('.satisfaction-block');
                if (unansweredSatisfaction) {
                    console.log('期待度選択後: 満足度にスクロールします');
                    setTimeout(() => {
                        scrollToElementCenter(unansweredSatisfaction);
                    }, 500);
                }
            }
        }

        function selectSatisfaction(element) {
            const categoryId = element.dataset.categoryId;
            const value = parseInt(element.dataset.value);
            
            // 同じカテゴリー内の他の満足度の円から選択を解除
            const categoryGroup = element.closest('.category-group');
            categoryGroup.querySelectorAll('.satisfaction-block .satisfaction-circle').forEach(circle => {
                circle.classList.remove('selected');
            });
            
            // クリックされた円を選択状態にする
            element.classList.add('selected');
            
            // 隠しフィールドに値を設定
            document.getElementById(`satisfaction_${categoryId}`).value = value;
            
            // デバッグ用のログ
            console.log(`満足度を設定: カテゴリー${categoryId}, 値${value}`);
            
            // 進捗を保存
            saveProgress();
            
            // 期待度が既に回答済みかチェック
            const expectationSelected = categoryGroup.querySelector('.expectation-block .satisfaction-circle.selected');
            
            if (expectationSelected) {
                // 期待度が回答済みの場合は、サブカテゴリーにスクロール
                console.log('期待度と満足度の両方が回答済みです。サブカテゴリーにスクロールします。');
                const subcategoryGroups = categoryGroup.querySelectorAll('.mb-8');
                if (subcategoryGroups.length > 0) {
                    // 最初のサブカテゴリーグループにスクロール（遅延を長めに設定）
                    setTimeout(() => {
                        scrollToElementCenter(subcategoryGroups[0]);
                    }, 800);
                }
            } else {
                // 期待度が未回答の場合は、期待度にスクロール
                const unansweredExpectation = categoryGroup.querySelector('.expectation-block');
                if (unansweredExpectation) {
                    console.log('満足度選択後: 期待度にスクロールします');
                    setTimeout(() => {
                        scrollToElementCenter(unansweredExpectation);
                    }, 500);
                }
            }
        }

        function selectSubResponse(element, subcategoryId, value) {
            // 同じサブカテゴリー内の他の円から選択を解除
            const categoryGroup = element.closest('.category-group');
            categoryGroup.querySelectorAll(`.satisfaction-circle[data-subcategory-id="${subcategoryId}"]`).forEach(circle => {
                circle.classList.remove('selected');
            });
            
            // クリックされた円を選択状態にする
            element.classList.add('selected');
            
            // 隠しフィールドに値を設定
            document.getElementById(`sub_response_${subcategoryId}`).value = value;
            
            // デバッグ用のログ
            console.log(`サブカテゴリー回答を設定: サブカテゴリー${subcategoryId}, 値${value}`);
            
            // 進捗を保存
            saveProgress();
            
            // 次の未回答項目に自動スクロール
            setTimeout(() => {
                // 次のサブカテゴリーの未回答を探す
                const currentSubcategoryId = subcategoryId;
                const subcategoryGroups = categoryGroup.querySelectorAll('.mb-8');
                let foundCurrent = false;
                
                for (const group of subcategoryGroups) {
                    // 現在のサブカテゴリーを見つけたら、次のサブカテゴリーを探す
                    if (foundCurrent) {
                        const unansweredSubResponse = group.querySelector('.satisfaction-circle:not(.selected)');
                        if (unansweredSubResponse) {
                            console.log('サブカテゴリー選択後: 次の未回答のサブカテゴリーを見つけました');
                            scrollToElement(group);
                            return;
                        }
                    }
                    
                    // 現在のサブカテゴリーかどうかをチェック
                    const subcategoryCircle = group.querySelector(`.satisfaction-circle[data-subcategory-id="${currentSubcategoryId}"]`);
                    if (subcategoryCircle) {
                        foundCurrent = true;
                    }
                }
                
                // 全て回答済みの場合は、次へボタンをハイライト表示
                const nextButton = categoryGroup.querySelector('.next-button');
                if (nextButton) {
                    nextButton.classList.add('bg-pink-600');
                    nextButton.classList.add('animate-pulse');
                    
                    // 3秒後にハイライトを解除
                    setTimeout(() => {
                        nextButton.classList.remove('bg-pink-600');
                        nextButton.classList.remove('animate-pulse');
                    }, 3000);
                }
            }, 300);
        }

        function scrollToElement(element) {
            if (!element) return;
            
            // 要素が表示されるまで待機
            setTimeout(() => {
                const container = document.querySelector('.overflow-y-auto');
                if (!container) return;
                
                // ヘッダーの高さを考慮（60px）
                const headerHeight = 60;
                
                // 要素の位置を計算
                const elementTop = element.offsetTop;
                
                // スクロール位置を計算（要素を中央に配置）
                const targetScroll = elementTop - headerHeight - 100;
                
                // スムーズにスクロール
                container.scrollTo({
                    top: Math.max(0, targetScroll),
                    behavior: 'smooth'
                });
                
                // デバッグ用のログ
                console.log(`スクロール位置: ${targetScroll}, 要素:`, element);
            }, 300);
        }

        function scrollToNextUnanswered() {
            const currentGroup = document.querySelector('.category-group:not(.hidden)');
            if (!currentGroup) return;
            
            console.log('未回答項目を探しています...');
            
            // 期待度の未回答を探す
            const unansweredExpectation = currentGroup.querySelector('.expectation-block .satisfaction-circle:not(.selected)');
            if (unansweredExpectation) {
                console.log('未回答の期待度を見つけました');
                scrollToElement(unansweredExpectation);
                return;
            }
            
            // 満足度の未回答を探す
            const unansweredSatisfaction = currentGroup.querySelector('.satisfaction-block .satisfaction-circle:not(.selected)');
            if (unansweredSatisfaction) {
                console.log('未回答の満足度を見つけました');
                scrollToElement(unansweredSatisfaction);
                return;
            }
            
            // サブカテゴリーの未回答を探す
            const subcategoryGroups = currentGroup.querySelectorAll('.mb-8');
            for (const group of subcategoryGroups) {
                const unansweredSubResponse = group.querySelector('.satisfaction-circle:not(.selected)');
                if (unansweredSubResponse) {
                    console.log('未回答のサブカテゴリーを見つけました');
                    // サブカテゴリーの場合は、グループ全体を表示するようにスクロール
                    scrollToElement(group);
                    return;
                }
            }
            
            console.log('未回答項目が見つかりませんでした');
            
            // 全て回答済みの場合は、次へボタンをハイライト表示
            const nextButton = currentGroup.querySelector('.next-button');
            if (nextButton) {
                nextButton.classList.add('bg-pink-600');
                nextButton.classList.add('animate-pulse');
                
                // 3秒後にハイライトを解除
                setTimeout(() => {
                    nextButton.classList.remove('bg-pink-600');
                    nextButton.classList.remove('animate-pulse');
                }, 3000);
            }
        }

        function showNextCategory(currentCategoryId) {
            const currentGroupElement = document.querySelector(`.category-group[data-category-id="${currentCategoryId}"]`);
            if (!currentGroupElement) return;
            
            const currentIndex = parseInt(currentGroupElement.dataset.index);
            const nextIndex = currentIndex + 1;
            if (nextIndex >= totalCategories) return;
            
            // 現在のカテゴリーの回答が完了しているかチェック
            const isComplete = checkCategoryCompletion(currentGroupElement);
            if (!isComplete) {
                return; // 回答が完了していない場合は次に進まない
            }
            
            // 現在のグループを非表示
            currentGroupElement.classList.add('hidden');
            currentGroupElement.classList.remove('block');
            
            // 次のグループを表示
            const nextGroup = document.querySelector(`.category-group[data-index="${nextIndex}"]`);
            if (nextGroup) {
                // グループ番号とカテゴリーインデックスの更新
                const groupNumber = parseInt(nextGroup.dataset.group);
                if (groupNumber !== currentGroup) {
                    currentGroup = groupNumber;
                    localStorage.setItem('currentGroup', currentGroup);
                }
                currentCategoryIndex = nextIndex;
                localStorage.setItem('currentCategoryIndex', currentCategoryIndex);
                updateCategoryNumbers();
                
                // 次のグループを表示
                nextGroup.classList.remove('hidden');
                nextGroup.classList.add('block');
                
                // スクロールを一度だけ実行
                const scrollContainer = document.querySelector('.overflow-y-auto');
                if (scrollContainer) {
                    // 次のカテゴリーの最初の要素（期待度ブロック）を取得
                    const expectationBlock = nextGroup.querySelector('.expectation-block');
                    if (expectationBlock) {
                        // スムーズにスクロール
                        setTimeout(() => {
                            scrollToElementCenter(expectationBlock);
                        }, 100);
                    }
                }
            }
        }

        function showPreviousCategory(currentCategoryId) {
            const currentGroupElement = document.querySelector(`.category-group[data-category-id="${currentCategoryId}"]`);
            if (!currentGroupElement) return;

            const currentIndex = parseInt(currentGroupElement.dataset.index);
            const prevIndex = currentIndex - 1;
            if (prevIndex < 0) return;

            // 現在のグループを非表示
            currentGroupElement.classList.add('hidden');
            currentGroupElement.classList.remove('block');

            // 前のグループを表示
            const prevGroup = document.querySelector(`.category-group[data-index="${prevIndex}"]`);
            if (prevGroup) {
                prevGroup.classList.remove('hidden');
                prevGroup.classList.add('block');
                
                // グループ番号とカテゴリーインデックスの更新
                const groupNumber = parseInt(prevGroup.dataset.group);
                if (groupNumber !== currentGroup) {
                    currentGroup = groupNumber;
                    localStorage.setItem('currentGroup', currentGroup);
                }
                currentCategoryIndex = prevIndex;
                localStorage.setItem('currentCategoryIndex', currentCategoryIndex);
                updateCategoryNumbers();
                
                // スクロールを上に移動
                const scrollContainer = document.querySelector('.overflow-y-auto');
                scrollContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        function updateCategoryNumbers() {
            document.querySelectorAll('.category-number').forEach(number => {
                const groupNum = parseInt(number.dataset.group);
                number.classList.remove('active', 'completed');
                
                if (groupNum === currentGroup) {
                    number.classList.add('active');
                } else if (groupNum < currentGroup) {
                    number.classList.add('completed');
                }
            });
        }

        function saveProgress() {
            // 既に保存中の場合は処理をスキップ
            if (isSaving) {
                return;
            }

            // 既存のタイマーをクリア
            if (saveTimeout) {
                clearTimeout(saveTimeout);
            }

            // 新しいタイマーを設定（1秒後に実行）
            saveTimeout = setTimeout(() => {
                // フォームデータを取得
                const form = document.getElementById('surveyForm');
                const formData = new FormData(form);

                // 期待度、満足度、サブカテゴリーの回答を保存
                const expectations = {};
                const satisfactions = {};
                const subResponses = {};
                
                formData.forEach((value, key) => {
                    if (key.startsWith('expectations[')) {
                        const categoryId = key.match(/\[(\d+)\]/)[1];
                        expectations[categoryId] = value;
                    } else if (key.startsWith('satisfactions[')) {
                        const categoryId = key.match(/\[(\d+)\]/)[1];
                        satisfactions[categoryId] = value;
                    } else if (key.startsWith('sub_responses[')) {
                        const subcategoryId = key.match(/\[(\d+)\]/)[1];
                        subResponses[subcategoryId] = value;
                    }
                });
                
                // ローカルストレージに保存
                localStorage.setItem('expectations', JSON.stringify(expectations));
                localStorage.setItem('satisfactions', JSON.stringify(satisfactions));
                localStorage.setItem('subResponses', JSON.stringify(subResponses));
                localStorage.setItem('currentGroup', currentGroup.toString());
                localStorage.setItem('currentCategoryIndex', currentCategoryIndex.toString());
                
                // サーバーにも保存
                const saveStatus = document.getElementById('saveStatus');
                saveStatus.classList.remove('hidden');
                saveStatus.textContent = '保存中...';
                
                isSaving = true;
                
                fetch('{{ route("survey.saveDraft") }}', {
            method: 'POST',
            headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
                .then(result => {
                    console.log('Save response:', result);
                    saveStatus.textContent = '保存しました';
                    setTimeout(() => {
                        saveStatus.classList.add('hidden');
                    }, 2000);
            })
            .catch(error => {
                    console.error('Error saving draft:', error);
                    saveStatus.textContent = '保存に失敗しました';
                    setTimeout(() => {
                        saveStatus.classList.add('hidden');
                    }, 2000);
                })
                .finally(() => {
                    isSaving = false;
                });
            }, 1000); // 1秒のデバウンス時間
        }

        function checkGroupCompletion() {
            const currentGroupElement = document.querySelector(`.category-group[data-group="${currentGroup}"]`);
            if (!currentGroupElement) return;

            const allCircles = currentGroupElement.querySelectorAll('.satisfaction-circle');
            const allSelected = Array.from(allCircles).every(circle => circle.classList.contains('selected'));

            if (allSelected) {
                completedGroups.add(currentGroup);
                updateCategoryNumbers();
            }
        }

        // スクロール位置を保存する関数
        function saveScrollPosition() {
            const scrollContainer = document.querySelector('.overflow-y-auto');
            if (scrollContainer) {
                localStorage.setItem('scrollPosition', scrollContainer.scrollTop.toString());
            }
        }

        // スクロールイベントで位置を保存
        const scrollContainer = document.querySelector('.overflow-y-auto');
        if (scrollContainer) {
            scrollContainer.addEventListener('scroll', debounce(saveScrollPosition, 500));
        }

        // ページがアンロードされる前にスクロール位置を保存
        window.addEventListener('beforeunload', saveScrollPosition);

        // デバウンス関数
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // カテゴリーの回答が完了しているかチェックする関数
        function checkCategoryCompletion(categoryGroup) {
            console.log('カテゴリーの回答完了チェックを実行します');
            
            // 期待度の回答をチェック
            const expectationCircle = categoryGroup.querySelector('.expectation-block .satisfaction-circle.selected');
            if (!expectationCircle) {
                console.log('期待度が未回答です');
                alert('期待度に回答してください。');
                const unansweredExpectation = categoryGroup.querySelector('.expectation-block .satisfaction-circle:not(.selected)');
                if (unansweredExpectation) {
                    scrollToElement(unansweredExpectation);
                }
                return false;
            }
            
            // 満足度の回答をチェック
            const satisfactionCircle = categoryGroup.querySelector('.satisfaction-block .satisfaction-circle.selected');
            if (!satisfactionCircle) {
                console.log('満足度が未回答です');
                alert('満足度に回答してください。');
                const unansweredSatisfaction = categoryGroup.querySelector('.satisfaction-block .satisfaction-circle:not(.selected)');
                if (unansweredSatisfaction) {
                    scrollToElement(unansweredSatisfaction);
                }
                return false;
            }
            
            // サブカテゴリーの回答をチェック
            if (!checkSubcategoryAnswers(categoryGroup)) {
                return false;
            }
            
            console.log('カテゴリーの回答が完了しています');
            // 全ての回答が完了している場合はtrueを返す
            return true;
        }

        // サブカテゴリーの回答をチェックする関数を改善
        function checkSubcategoryAnswers(categoryGroup) {
            console.log('サブカテゴリーの回答チェックを実行します');
            const subcategoryGroups = categoryGroup.querySelectorAll('.mb-8');
            for (const group of subcategoryGroups) {
                const circles = group.querySelectorAll('.satisfaction-circle');
                const allAnswered = Array.from(circles).some(circle => circle.classList.contains('selected'));
                if (!allAnswered) {
                    console.log('サブカテゴリーが未回答です');
                    alert('サブカテゴリーの全ての項目に回答してください。');
                    // サブカテゴリーの場合は、グループ全体を表示するようにスクロール
                    scrollToElement(group);
                    return false;
                }
            }
            console.log('サブカテゴリーの回答が完了しています');
            return true;
        }

        // 要素を中央に表示するスクロール関数を改善
        function scrollToElementCenter(element) {
            if (!element) return;
            
            const container = document.querySelector('.overflow-y-auto');
            if (!container) return;
            
            // ヘッダーの高さを考慮（60px）
            const headerHeight = 60;
            
            // 要素の位置を計算
            const elementTop = element.offsetTop;
            const elementHeight = element.offsetHeight;
            const containerHeight = container.clientHeight;
            
            // 要素を中央に配置するためのスクロール位置を計算
            const targetScroll = elementTop - headerHeight - (containerHeight / 2) + (elementHeight / 2);
            
            // スムーズにスクロール（behaviorをsmoothに設定）
            container.scrollTo({
                top: Math.max(0, targetScroll),
                behavior: 'smooth'
            });
            
            // デバッグ用のログ
            console.log(`中央にスクロール: 位置${targetScroll}, 要素:`, element);
        }

        // 期待度と満足度の両方が回答済みかチェックする関数
        function checkExpectationAndSatisfaction(categoryGroup) {
            const expectationCircle = categoryGroup.querySelector('.expectation-block .satisfaction-circle.selected');
            const satisfactionCircle = categoryGroup.querySelector('.satisfaction-block .satisfaction-circle.selected');
            
            return expectationCircle && satisfactionCircle;
        }

        // 期待度と満足度の両方が回答済みの場合に、サブカテゴリーにスクロールする関数
        function scrollToSubcategoriesIfNeeded(categoryGroup) {
            if (checkExpectationAndSatisfaction(categoryGroup)) {
                console.log('期待度と満足度の両方が回答済みです。サブカテゴリーにスクロールします。');
                const subcategoryGroups = categoryGroup.querySelectorAll('.mb-8');
                if (subcategoryGroups.length > 0) {
                    // 最初のサブカテゴリーグループにスクロール
                    scrollToElementCenter(subcategoryGroups[0]);
                    return true;
                }
            }
            return false;
        }

        // 未回答カテゴリーを表示する関数
        function showUnansweredCategory(category, allCategories) {
            // 他のカテゴリーを非表示
            allCategories.forEach(cat => {
                cat.classList.add('hidden');
                cat.classList.remove('block');
            });
            
            // 未回答カテゴリーを表示
            category.classList.remove('hidden');
            category.classList.add('block');
            
            // インデックスとグループ番号を更新
            currentCategoryIndex = parseInt(category.dataset.index);
            currentGroup = parseInt(category.dataset.group);
            
            // ローカルストレージを更新
            localStorage.setItem('currentCategoryIndex', currentCategoryIndex.toString());
            localStorage.setItem('currentGroup', currentGroup.toString());
            
            // カテゴリー番号を更新
            updateCategoryNumbers();
            
            // 未回答項目までスクロール
            setTimeout(() => {
                scrollToNextUnanswered();
            }, 100);
        }
    </script>
    </body>
</html>

