<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>アンケート完了</title>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+Antique:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        .logo-spin {
            animation: spin 20s linear infinite;
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @media (max-width: 1024px) {
                .main{
                    width: 0%;
                }
                .header{
                    display: none;
                }
                .text1{
                    color: #FF768D;
                    font-size: 20px;
                    white-space: nowrap;
                }
                .text2{
                    color: #FF768D;
                    font-size: 20px;
                    white-space: nowrap;
                }
                .background{
                    width: 85%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    margin-top: -30px;
                    align-items: center;
                }
                .logo{
                    width: 200px;
                    height: 200px;
                    align-items: center;
                    margin-left: -30px;
                    margin-top: -50px;
                }
                .bizbuddy{
                    margin-top: -45px;
                }
            }
    </style>
</head>
<body class="font-mono bg-gradient-to-b from-[#F5F6F8] to-white min-h-screen">
    <!-- ヘッダー -->
    <header class="header w-full h-[60px] bg-[#E8EBF0] backdrop-blur-md shadow-sm rounded-br-3xl rounded-bl-3xl fixed top-0 left-0 z-[80]">
        <div class="max-w-screen-xl mx-auto px-6 h-full flex items-center justify-between">
            <h1 class="text-2xl font-bold text-customPink">職場環境改善アンケート</h1>
            <div class="flex items-center space-x-2 text-customNavy">
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

    <!-- メインコンテンツ -->
    <main class="min-h-screen flex items-center justify-center pt-[60px]">
        <!-- 白の背景レイヤー -->
        <div class="absolute inset-0 bg-white/40 backdrop-blur-sm"></div>
        
        <div class="background relative text-center fade-in bg-white/80 backdrop-blur-md rounded-[40px] shadow-xl p-24 mx-auto w-[800px]">
            <!-- BizBuddyロゴ -->
            <div class="logo relative w-60 h-60 mx-auto mb-16">
                <img src="{{ asset('image/BB_large.png') }}" alt="BizBuddy" class="w-full h-full object-contain logo-spin drop-shadow-lg">
            </div>

            <!-- BizBuddy テキスト -->
            <h2 class="bizbuddy text-5xl font-bold text-customNavy mb-16 tracking-wide pulse">BizBuddy</h2>

            <!-- 完了メッセージ -->
            <div class="space-y-8">
                <p class="text1 text-3xl font-bold text-customPink">アンケートは完了です</p>
                <p class="text2 text-xl text-customNavy">ご協力ありがとうございました</p>
            </div>
        </div>
    </main>
</body>
</html> 