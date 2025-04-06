<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<section class="splide">
<div class="splide__track top-0 left-0 w-full h-screen">
    <ul class="splide__list">
        
        <li class="splide__slide">
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="relative bg-white p-8 rounded-3xl w-[1000px] h-[600px] shadow-lg">
            <div class="absolute inset-0 bg-[rgba(255,118,141,0.85)] rounded-tl-3xl rounded-bl-3xl" style="clip-path: polygon(0 0, 80% 0, 95% 50%, 80% 100%, 0 100%); z-0"></div>
            <div class="text-center space-y-1 mt-10 relative z-10">
                <h1 class="text-3xl font-bold text-white">BizBuddyが自動で</h1>
                <h2 class="text-3xl font-bold text-white">アンケートの集計・分析を行い</h2>
                <h2 class="text-3xl font-bold text-white">最適な施策を提案します</h2>
            </div>
            <div class="relative">
                <div class="absolute z-50 w-2/5 top-28"><img src="./image/tutorial1.png" alt="tutorial1" class="rounded-lg mt-8 ml-72"></div>
                <div class="absolute top-10 left-16 z-20"><img src="./image/ggraph1.png" alt="tutorial2" class="rounded-2xl"></div>
                <div class="absolute w-2/5 top-6 right-16 z-50"><img src="./image/ggraph2.png" alt="tutorial3"></div>
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-6 mb-10">
                <div class="w-3 h-3 bg-white rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
            </div>

            <button class="next-btn absolute right-4 bottom-4 bg-customNavy text-white px-6 py-2 rounded-md shadow-sm flex items-center space-x-2 mr-10 mb-9">
                <span class="font-semibold">次へ</span>
                <span>➡</span>
            </button>
            </div>
        </div>
        </li>
        <li class="splide__slide">
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="relative bg-[rgba(255,118,141,0.85)] p-8 rounded-3xl w-[1000px] h-[600px] shadow-lg">
            <div class="absolute inset-0 bg-white rounded-tl-3xl rounded-bl-3xl" style="clip-path: polygon(0 0, 80% 0, 95% 50%, 80% 100%, 0 100%); z-0"></div>
            <div class="flex">
                <img src="./image/ggraph3.png" alt="graph3" class="relative z-10 max-w-[280px] h-auto">
                <div class="text-customNavy relative z-10 ml-[90px] mt-[200px]">
                    <h1 class="text-3xl font-bold">BizBuddyが提案する施策をもとに</h1>
                    <h2 class="text-3xl font-bold ml-8">改善プランを考えましょう</h2>
                </div>
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-6 mb-10">
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-customNavy rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
            </div>
        <button class="next-btn absolute right-4 bottom-4 bg-customNavy text-white px-6 py-2 rounded-md shadow-sm flex items-center space-x-2 mr-10 mb-9">
            <span class="font-semibold">次へ</span>
            <span>➡</span>
        </button>
        </div>
        </div>
        </li>
        <li class="splide__slide">
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="relative bg-[#20487C] p-8 rounded-3xl w-[1000px] h-[600px] shadow-lg">
            <div class="absolute inset-0 bg-[#FE899D] rounded-tl-3xl rounded-bl-3xl" style="clip-path: polygon(0 0, 80% 0, 95% 50%, 80% 100%, 0 100%); z-0"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold text-white text-center mt-12">BizBuddyが計画実行をサポートします</h1>
            </div>
            <div class="relative">
                <div class="absolute z-50 w-2/5 top-20"><img src="./image/tracking1.png" alt="tutorial1" class="rounded-lg mt-8 ml-72"></div>
                <div class="absolute top-10 left-16 z-20"><img src="./image/tracking2.png" alt="tutorial2" class="rounded-2xl"></div>
                <div class="absolute w-2/5 top-56 -right-12 z-50"><img src="./image/tracking3.png" alt="tutorial3"></div>
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-6 mb-10">
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-white rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
            </div>
        <button class="next-btn absolute right-4 bottom-4 bg-white text-white px-6 py-2 rounded-md shadow-sm flex items-center space-x-2 mr-10 mb-9">
            <span class="text-customNavy font-semibold">次へ</span>
            <span class="text-customNavy">➡</span>
        </button>
        </div>
        </div>
        </li>
        <li class="splide__slide">
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="relative bg-[#20487C] p-8 rounded-3xl w-[1000px] h-[600px] shadow-lg z-0">
            <div class="flex">
                <div class="relative z-20">
                    <img src="./image/tracking5.png" alt="graph3" class="absolute max-w-[280px] h-auto left-4 top-20">
                    <img src="./image/tracking6.png" alt="graph3" class="absolute max-w-[280px] h-auto top-40 left-40">
                </div>
                <div class="text-white relative z-10 ml-[500px] mt-[200px]">
                    <h1 class="text-3xl font-bold">BizBuddyは結果を分析して</h1>
                    <h2 class="text-3xl font-bold -ml-6">施策をブラッシュアップします</h2>
                </div>
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-6 mb-10">
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-[rgba(0,33,77,0.3)] rounded-full"></div>
                <div class="w-3 h-3 bg-white rounded-full"></div>
            </div>
        <button class="next-btn absolute right-4 bottom-4 bg-white text-customNavy px-6 py-2 rounded-md shadow-sm flex items-center space-x-2 mr-10 mb-9">
            <span class="font-semibold">はじめる</span>
        </button>
        </div>
        </div>
        </li>
    </ul>
</section>
<!-- tutorial.js をVite経由で読み込む -->
@vite('resources/js/app.js')
</body>
</html>