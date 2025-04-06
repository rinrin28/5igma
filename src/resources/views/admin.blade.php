<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-mono">
    <main class="flex h-screen">
            <div class="w-24 h-screen bg-customGray flex flex-col items-center py-5 rounded-tr-3xl rounded-br-3xl mr-6 fixed">
                <div class="flex items-center justify-center">
                    <img src="./image/logo.png" alt="BIzBuddy">
                </div>
                <div class="space-y-7 flex flex-col my-auto mt-[230px]">
                    <a href="{{ route('admin') }}" class="relative">
                        <img src="./image/setting.png" alt="setting">
                        <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                    </a>
                </div>
                <div class="mt-auto">
                    <img src="./image/usericon.png" alt="icon">
                </div>
            </div>

            <div class="flex flex-col ml-28 w-full">
            <header class="w-full h-16 bg-customGray flex items-center px-6 ml-auto rounded-br-3xl rounded-bl-3xl">
                <h1 class="text-xl font-bold text-customPink font-mono mr-20">Settings</h1>
                <nav class="ml-8 flex items-center space-x-2 text-customNavy">
                    <span class="text-lg font-semibold font-mono">金堂印刷株式会社</span>
            </header>
            <div class="w-full h-screen bg-customGray shadow-inner mt-6 rounded-3xl p-6">
                <div class="bg-white rounded-[30px] shadow-md p-10 h-[650px] w-[1280px]">
                <div class="flex items-center space-x-2 mb-2">
                <img src="./image/Settings.png" alt="settings" class="w-8 h-8">
                <h1 class="text-3xl font-bold text-customNavy">設定</h1>
            </div>
            <p class="text-sm text-[#82868B] font-medium mb-6">全社使用上の製品に関する設定</p>
            <h2 class="text-xl font-bold text-customNavy mb-1">職場環境改善アンケート</h2>
            <p class="text-sm text-[#82868B] mb-6 leading-relaxed">
            半期に一回全社一斉にアンケートを実施し、会社全体或いは部署全体のボトルネックを明らかにします
            </p>
            <p class="text-base font-bold text-customNavy mb-1">実施期間設定</p>
            <p class="text-xs text-[#82868B] mb-4">半期に一度アンケートを実施し、会社全体或いは部署全体のボトルネックを明らかにします</p>
            
            <form action="{{ route('admin.update-survey-period') }}" method="POST">
                @csrf
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 space-y-4 w-[620px]">
                    <div class="flex items-center space-x-4">
                        <div class="w-20 font-bold text-xl text-customNavy">上半期</div>
                        <input type="date" name="first_half_start" class="bg-white border border-gray-300 text-gray-900 text-lg px-4 py-1 rounded-md w-[200px]" required />
                        <img src="./image/right.png" alt="right">
                        <div class="bg-[#82868B] text-white text-lg px-4 py-1 rounded-md w-[200px]" id="first_half_end"></div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 font-bold text-customNavy text-xl">下半期</div>
                        <input type="date" name="second_half_start" class="bg-white border border-gray-300 text-gray-900 text-lg px-4 py-1 rounded-md w-[200px]" required />
                        <img src="./image/right.png" alt="right">
                        <div class="bg-[#82868B] text-white text-lg px-4 py-1 rounded-md w-[200px]" id="second_half_end"></div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="bg-customPink text-white px-6 py-2 rounded-md hover:bg-pink-600 transition-colors">
                            設定を保存
                        </button>
                    </div>
                </div>
            </form>

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const firstHalfStartInput = document.querySelector('input[name="first_half_start"]');
    const secondHalfStartInput = document.querySelector('input[name="second_half_start"]');
    const firstHalfEndDiv = document.getElementById('first_half_end');
    const secondHalfEndDiv = document.getElementById('second_half_end');

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}/${month}/${day}`;
    }

    function updateEndDate(startInput, endDiv) {
        if (startInput.value) {
            const startDate = new Date(startInput.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 13);
            endDiv.textContent = formatDate(endDate);
        } else {
            endDiv.textContent = '年/月/日';
        }
    }

    function updateOtherHalf(sourceInput, targetInput, isFirstHalf) {
        if (sourceInput.value) {
            const sourceDate = new Date(sourceInput.value);
            const targetDate = new Date(sourceDate);
            targetDate.setMonth(sourceDate.getMonth() + 6);
            targetInput.value = targetDate.toISOString().split('T')[0];
            updateEndDate(targetInput, isFirstHalf ? secondHalfEndDiv : firstHalfEndDiv);
        }
    }

    firstHalfStartInput.addEventListener('change', function() {
        updateEndDate(this, firstHalfEndDiv);
        updateOtherHalf(this, secondHalfStartInput, true);
    });

    secondHalfStartInput.addEventListener('change', function() {
        updateEndDate(this, secondHalfEndDiv);
        updateOtherHalf(this, firstHalfStartInput, false);
    });

    // 初期表示
    firstHalfEndDiv.textContent = '年/月/日';
    secondHalfEndDiv.textContent = '年/月/日';
});
</script>
</body>
</html>