<!DOCTYPE html>
<html lang="en">
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
    <body>
        @php
            $currentDeptId = request('dept_id', Auth::user()->department_id ?? null);
            $title = $title ?? request()->route()->getName();
            $currentYear = request('year', date('Y'));
            $currentPeriod = request('period', 'first');
            
            // 利用可能な期間を取得
            if (auth()->user()->role === 'executive') {
                $surveyService = app(App\Services\ExecutiveSurveyService::class);
                $availablePeriods = $surveyService->getAvailablePeriods($currentYear);
            } else {
                $surveyService = app(App\Services\SurveyService::class);
                $availablePeriods = $surveyService->getAvailablePeriods($currentYear, $currentDeptId);
            }
        @endphp
        <div class="flex h-screen">
            @auth
                <x-sidebar :currentDeptId="$currentDeptId" />
                <div class="flex-1 flex-col ml-28 w-full">
                    @if(isset($departments))
                        <x-header 
                            :currentDeptId="$currentDeptId" 
                            :departments="$departments" 
                            :title="$title" 
                            :currentYear="$currentYear"
                            :currentPeriod="$currentPeriod"
                            :availablePeriods="$availablePeriods"
                        />
                    @endif
                    
                    <!-- メインコンテンツ -->
                    <main class="w-full bg-customGray shadow-inner mt-6 rounded-3xl p-4">
                        {{ $slot }}
                    </main>
                </div>
            @else
                <!-- ログイン画面など、サイドバーとヘッダーが不要な画面用 -->
                <main class="w-full">
                    {{ $slot }}
                </main>
            @endauth
        </div>

        <!-- 共通のJavaScript -->
        <script src="{{ asset('js/accordion.js') }}"></script>
    </body>
</html>
