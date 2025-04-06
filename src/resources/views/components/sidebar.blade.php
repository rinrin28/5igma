@props(['currentDeptId'])

<div class="w-24 h-screen bg-customGray flex flex-col items-center py-5 rounded-tr-3xl rounded-br-3xl mr-6 fixed">
    <div class="flex items-center justify-center">
    @if(auth()->user()->role === 'executive')
        <img src="../image/logo.png" alt="BIzBuddy">
        @else
        <img src="./image/logo.png" alt="BIzBuddy">
        @endif
    </div>
    <div class="space-y-7 flex flex-col my-auto">
        @if(auth()->user()->role === 'executive')
            <a href="{{ route('executive.dashboard') }}" class="relative">
                <img src="../image/dashboard{{ request()->routeIs('executive.dashboard') ? '2' : '' }}.png" alt="dashboard">
                @if(request()->routeIs('executive.dashboard'))
                    <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                @endif
            </a>
            <!-- <a href="{{ route('executive.analytics') }}" class="relative">
                <img src="../image/analytics{{ request()->routeIs('executive.analytics') ? '2' : '' }}.png" alt="analytics">
                @if(request()->routeIs('executive.analytics'))
                    <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                @endif
            </a> -->
        @else
            <a href="{{ route('dashboard', ['dept_id' => $currentDeptId]) }}" class="relative">
                <img src="./image/dashboard{{ request()->routeIs('dashboard') ? '2' : '' }}.png" alt="dashboard">
                @if(request()->routeIs('dashboard'))
                    <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                @endif
            </a>
            <a href="{{ route('analytics', ['dept_id' => $currentDeptId]) }}" class="relative">
                <img src="./image/analytics{{ request()->routeIs('analytics') ? '2' : '' }}.png" alt="analytics">
                @if(request()->routeIs('analytics'))
                    <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                @endif
            </a>
            <a href="{{ route('tracking', ['dept_id' => $currentDeptId]) }}" class="relative">
                <img src="./image/tracking{{ request()->routeIs('tracking') ? '2' : '' }}.png" alt="tracking">
                @if(request()->routeIs('tracking'))
                    <div class="absolute top-1/2 transform -translate-y-1/2 w-3 h-5 bg-customPink rounded-r-full -ml-6"></div>
                @endif
            </a>
        @endif
    </div>
    <div class="mt-auto">
    @if(auth()->user()->role === 'executive')
        <img src="../image/usericon.png" alt="icon">
        @else
        <img src="./image/usericon.png" alt="icon">
        @endif
    </div>
</div> 