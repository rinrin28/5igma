@props(['currentDeptId', 'departments', 'title', 'currentYear', 'currentPeriod', 'availablePeriods' => []])

<header class="w-full h-16 bg-customGray flex items-center px-6 ml-auto rounded-br-3xl rounded-bl-3xl">
    <h1 class="text-xl font-bold text-customPink font-mono mr-20">
        @switch($title)
            @case('Dashboard')
                ダッシュボード
                @break
            @case('Analytics')
                分析
                @break
            @case('Planning')
                計画
                @break
            @case('Tracking')
                追跡
                @break
            @default
                {{ $title }}
        @endswitch
    </h1>
    
    <!-- Executiveバッジ（roleがexecutiveのときだけ表示） -->
    @if(auth()->user()->role === 'executive')
        <div class="mr-8">
            <div
                class="bg-customGreen text-white rounded-md flex items-center justify-center font-bold"
                style="width: 160px; height: 38px;"
            >
                Exective
            </div>
        </div>
    @endif

    <!-- 部署ナビゲーション（executive のみ表示） -->
    <!-- 部署ナビゲーション -->
    <nav class="ml-32 flex items-center space-x-2 text-customNavy">
    <span class="text-lg font-semibold font-mono">金堂印刷株式会社</span>
    <span class="text-customNavy">/</span>
    @php
        $current = $currentDeptId === null
            ? (object)['id' => null, 'name' => '経営陣']
            : $departments->firstWhere('id', $currentDeptId);
        $currentRoute = request()->route()->getName();
    @endphp

    @if(auth()->user()->role === 'executive')
        <!-- Executiveの場合はアコーディオンUI -->
        <div class="accordion-container relative">
            <div class="accordion-header flex items-center">
                <span class="accordion-title text-lg text-customNavy hover:text-gray-700 font-mono">
                    {{ $current->name }}
                </span>
                <span class="accordion-button text-customLightPink cursor-pointer ml-1">▶</span>
            </div>
            <div class="accordion-body absolute top-full left-[-20px] bg-[#DADEE5] rounded-bl-2xl rounded-br-2xl w-[120px] shadow transition-all duration-300 ease-in-out overflow-hidden z-50 hidden border-t-4 border-customBlue">
                <div class="text-sm text-customNavy">
                    <ul class="space-y-4 py-4">
                        <li class="text-center">
                            <span class="block py-2 px-4">金堂印刷株式会社</span>
                        </li>
                        <li class="text-center">
                            <span class="block py-2 px-4 mt-1">{{ $current->name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <!-- Executive以外の場合は単純に部署名を表示 -->
        <span class="text-lg text-customNavy font-mono mt-1">{{ $current->name }}</span>
    @endif
</nav>

    <!-- 年度・期間選択 -->
    <div class="ml-auto flex items-center space-x-6">
        <div class="accordion-container relative">
            <div class="accordion-header flex items-center">
                <span class="accordion-title text-lg font-semibold text-customNavy font-mono">{{ $currentYear }}</span>
                <span class="accordion-button text-customLightPink cursor-pointer ml-1">▶</span>
            </div>
            <div class="accordion-body absolute top-full right-0 bg-[#DADEE5] rounded-bl-2xl rounded-br-2xl p-4 w-[70px] mt-2 shadow transition-all duration-300 ease-in-out overflow-hidden z-50 hidden border-t-4 border-customBlue">
                <ul class="space-y-2">
                    @php
                        $years = range(date('Y'), date('Y') - 2);
                    @endphp
                    @foreach($years as $year)
                        <li>
                            @if(auth()->user()->role === 'executive')
                                <a href="{{ route(request()->route()->getName(), ['year' => $year, 'period' => $currentPeriod]) }}" 
                                   class="block py-2 hover:text-customPink transition-colors {{ $year == $currentYear ? 'text-customPink font-bold' : '' }}">
                                    {{ $year }}
                                </a>
                            @else
                                <a href="{{ route(request()->route()->getName(), ['dept_id' => $currentDeptId, 'year' => $year, 'period' => $currentPeriod]) }}" 
                                   class="block py-2 hover:text-customPink transition-colors {{ $year == $currentYear ? 'text-customPink font-bold' : '' }}">
                                    {{ $year }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- 期間選択 -->
        <div class="accordion-container relative">
            <div class="accordion-header flex items-center">
                <span class="accordion-title text-sm text-customNavy font-mono">
                    {{ $currentPeriod === 'first' ? '上半期' : '下半期' }}
                </span>
                <span class="accordion-button text-customLightPink cursor-pointer ml-1">▶</span>
            </div>
            <div class="accordion-body absolute top-full right-0 bg-[#DADEE5] rounded-bl-2xl rounded-br-2xl p-4 w-[120px] mt-2 shadow transition-all duration-300 ease-in-out overflow-hidden z-50 hidden border-t-4 border-customBlue">
                <ul class="space-y-2">
                    @foreach($availablePeriods as $period)
                        <li>
                            @if(auth()->user()->role === 'executive')
                                <a href="{{ route(request()->route()->getName(), ['year' => $currentYear, 'period' => $period['value']]) }}" 
                                   class="block py-2 hover:text-customPink transition-colors {{ $currentPeriod == $period['value'] ? 'text-customPink font-bold' : '' }}">
                                    {{ $period['value'] === 'first' ? '上半期' : '下半期' }}
                                </a>
                            @else
                                <a href="{{ route(request()->route()->getName(), ['dept_id' => $currentDeptId, 'year' => $currentYear, 'period' => $period['value']]) }}" 
                                   class="block py-2 hover:text-customPink transition-colors {{ $currentPeriod == $period['value'] ? 'text-customPink font-bold' : '' }}">
                                    {{ $period['value'] === 'first' ? '上半期' : '下半期' }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordions = document.querySelectorAll('.accordion-container');
        
        accordions.forEach(accordion => {
            const header = accordion.querySelector('.accordion-header');
            const body = accordion.querySelector('.accordion-body');
            const button = accordion.querySelector('.accordion-button');
            
            header.addEventListener('click', () => {
                // 他のアコーディオンを閉じる
                accordions.forEach(otherAccordion => {
                    if (otherAccordion !== accordion) {
                        otherAccordion.querySelector('.accordion-body').classList.add('hidden');
                        otherAccordion.querySelector('.accordion-button').textContent = '▶';
                    }
                });
                
                // クリックされたアコーディオンの開閉
                body.classList.toggle('hidden');
                button.textContent = body.classList.contains('hidden') ? '▶' : '▼';
            });
        });
        
        // ページの他の部分をクリックしたときにアコーディオンを閉じる
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.accordion-container')) {
                accordions.forEach(accordion => {
                    accordion.querySelector('.accordion-body').classList.add('hidden');
                    accordion.querySelector('.accordion-button').textContent = '▶';
                });
            }
        });
    });
</script> 