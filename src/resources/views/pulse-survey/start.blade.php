<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+Antique:wght@700&display=swap" rel="stylesheet">
        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .container {
                max-width: 1440px;
                margin: 0 auto;
                width: 100%;
            }
            .main-container {
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
                min-height: 650px;
            }
            .accordion-menu {
                position: absolute;
                top: 100%;
                left: 0;
                width: 600px;
            }
            @media (max-width: 1024px) {
                .main-container {
                    flex-direction: column;
                    padding: 1rem;
                }
                .logo-section {
                    margin: 0 auto 2rem;
                }
                .survey-info {
                    width: 100%;
                    max-width: 420px;
                    margin: 0 auto;
                }
                .accordion-menu {
                    width: 90%;
                    max-width: 600px;
                }
            }
        </style>
    </head>
    <body class="font-mono">
        <div class="flex min-h-screen flex-col">
            <div class="flex flex-col flex-1"> 
                <header class="w-full h-[70px] bg-customGray flex items-center px-6 rounded-br-3xl rounded-bl-3xl">
                    <div class="container flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-customPink">施策パルスサーベイ</h1>
                        
                        <div class="flex items-center space-x-2 text-customNavy">
                            <span class="text-lg">金堂印刷株式会社</span>
                            <span class="text-customNavy">/</span>
                            <span class="text-sm text-customNavy">{{ $department->name ?? '部署なし' }}</span>
                            <span class="text-customNavy ml-4">{{ $user->name ?? 'ユーザー不明' }}</span>
                            <span class="text-customNavy">さん</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-semibold text-customNavy">{{ $survey->year ?? date('Y') }}</span>
                            <span class="text-sm text-customNavy">{{ $survey->period ?? '上半期' }}</span>
                        </div>
                    </div>
                </header>

                <main class="flex-1 flex items-center justify-center bg-customGray shadow-inner mt-6 rounded-3xl p-4">
                    <div class="bg-white mx-auto rounded-3xl shadow-md p-10 flex justify-between items-center main-container">
                        <!-- 左側：ロゴとタイトル -->
                        <div class="flex flex-col items-center space-y-4 logo-section ml-36">
                            <img src="{{ asset('image/BB_large.png') }}" alt="BizBuddy Logo" class="w-80 h-80 rounded-full shadow-lg mb-8" />
                            <h1 class="text-5xl font-semibold text-customNavy">BizBuddy</h1>
                        </div>

                        <!-- 右側：アンケート情報 -->
                        <div class="survey-info mr-32">
                            <h2 class="text-customLightPink text-3xl font-bold mb-6">施策パルスサーベイ</h2>
                            
                            <ul class="space-y-4 text-customNavy text-lg">
                                <li class="flex items-center">
                                    <img src="{{ asset('image/Clockloader.png') }}" alt="Clockloader" class="w-8 mr-4">
                                    5〜10分間
                                </li>
                                <li class="flex items-center">
                                    <img src="{{ asset('image/Lockperson.png') }}" alt="Lockperson" class="w-8 mr-4">
                                    <span><span class="text-customPink font-bold">完全匿名</span>で実施されます</span>
                                </li>
                                <li class="flex items-center">
                                    <img src="{{ asset('image/Save.png') }}" alt="save" class="w-8 mr-4">
                                    回答内容は自動保存されます
                                </li>
                                <li class="flex items-center">
                                    <img src="{{ asset('image/Calendar.png') }}" alt="calendar" class="w-8 mr-4">
                                    【回答期間】 2025/4/1 〜 4/14
                                </li>
                            </ul>

                            <div class="mt-4 text-center">
                                <label class="inline-flex items-center text-xs text-[#82868B]">
                                    <input type="checkbox" id="privacy-policy" class="w-4 h-4 mr-2 accent-customLightPink" />
                                    <span class="text-customPink">プライバシーポリシー</span>に同意
                                </label>
                            </div>

                            <div class="mt-2 space-y-3">
                                <form id="survey-form" method="POST" action="{{ route('pulse-survey.store', $survey) }}">
                                    @csrf
                                    <button type="button" id="start-survey" class="w-3/4 mx-auto block bg-customPink text-white text-lg py-3 rounded-xl shadow-md hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        アンケートを開始
                                    </button>
                                    <p class="text-center text-sm text-[#82868B]">または</p>
                                    <button type="button" id="resume-survey" class="w-3/5 bg-customLightPink text-white text-lg py-3 rounded-xl shadow-md hover:opacity-90 mx-auto block">
                                        アンケートを再開
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const accordionButton = document.querySelector('.accordion__button');
                const accordionBody = document.querySelector('.accordion__body');
                const nav = document.querySelector('nav');
                const privacyCheckbox = document.getElementById('privacy-policy');
                const startButton = document.getElementById('start-survey');
                const resumeButton = document.getElementById('resume-survey');
                
                // プライバシーポリシーの同意チェック
                privacyCheckbox.addEventListener('change', function() {
                    startButton.disabled = !this.checked;
                });

                // アンケート開始ボタンのクリックイベント
                startButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (privacyCheckbox.checked) {
                        window.location.href = "{{ route('pulse-survey.index', $survey) }}";
                    }
                });

                // アンケート再開ボタンのクリックイベント
                resumeButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('pulse-survey.index', $survey) }}";
                });

                // アコーディオンボタンのクリックイベント
                accordionButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = accordionButton.getAttribute('aria-expanded') === 'true';
                    accordionButton.setAttribute('aria-expanded', !isExpanded);
                    accordionBody.classList.toggle('hidden');
                    accordionButton.textContent = isExpanded ? '▶' : '▼';
                });

                // メニュー内のクリックイベント
                accordionBody.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // ナビゲーション外のクリックイベント
                document.addEventListener('click', function(e) {
                    if (!nav.contains(e.target)) {
                        accordionBody.classList.add('hidden');
                        accordionButton.setAttribute('aria-expanded', 'false');
                        accordionButton.textContent = '▶';
                    }
                });

                // ウィンドウリサイズ時の処理
                window.addEventListener('resize', function() {
                    if (window.innerWidth <= 1024) {
                        accordionBody.classList.add('hidden');
                        accordionButton.setAttribute('aria-expanded', 'false');
                        accordionButton.textContent = '▶';
                    }
                });
            });
        </script>
    </body>
</html>
