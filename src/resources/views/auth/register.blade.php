<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>login</title>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-cover bg-center bg-no-repeat font-mono text-gray-900 antialiased">
        <div class="absolute top-0 left-0 w-7/12 h-full bg-gray-200 skew-x-[-15deg] origin-top-left"></div>
        <div class="absolute top-0 left-0 w-full h-full flex justify-center items-center">
        <div class="w-11/12 h-5/6 bg-white shadow-[0_0_20px_20px_rgba(0,0,0,0.2)] relative overflow-hidden">
            <div class="absolute top-0 w-7/12 h-full left-[-0.7%] bg-gray-200 skew-x-[-15deg] origin-top-left"></div>
            <div class="w-full h-screen flex">
                <div class="w-1/2 flex justify-center items-center mb-28">
                <div class="w-96 flex flex-col justify-center items-center">
                    <img src="/image/BB_large.png" class="mx-auto z-40" alt="logo">
                    <h1 class="mt-4 text-5xl font-bold text-blue-950 z-40">BizBuddy</h1>
                </div>
                </div>
                <div class="w-1/2 p-10 flex flex-col justify-center z-40">
                <div class="w-96 mx-auto mb-24">
                    <h2 class="text-3xl font-bold text-gray-800 mb-5">Get Started</h2>
                    <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="my-8 relative">
                        <!-- UserName -->
                        <div class="">
                            <label for="name" class="text-sm text-gray-600 font-bold">UserName</label>
                            <div class="flex items-center">
                                <input id="name" type="password" name="name" class="w-11/12 px-4 py-3 border-gray-300 rounded-lg mb-2 bg-gray-200" required autocomplete="current-password">
                                <button class="absolute right-0 w-6 h-6 rounded-full bg-rose-400 flex justify-center items-center">
                                    <span class="text-white text-sm font-bold">?</span>
                                </button>
                            </div>
                            @error('name')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Email -->
                        <div class="">
                            <label for="email" class="text-sm text-gray-600 font-bold">E-mail</label>
                            <div class="flex items-center">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-11/12 px-4 py-3 border-gray-300 rounded-lg mb-2 bg-gray-200" required autofocus autocomplete="username">
                                <button class="absolute right-0 w-6 h-6 rounded-full bg-rose-400 flex justify-center items-center">
                                    <span class="text-white text-sm font-bold">?</span>
                                </button>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Password -->
                        <div class="">
                            <label for="password" class="text-sm text-gray-600 font-bold">Password</label>
                            <input id="password" type="password" name="password" class="w-11/12 px-4 py-3 border-gray-300 rounded-lg mb-2 bg-gray-200" required autocomplete="current-password">
                            @error('password')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Password Again -->
                        <div class="">
                            <label for="password_again" class="text-sm text-gray-600 font-bold">Password Again</label>
                            <input id="password_again" type="password" name="password" class="w-11/12 px-4 py-3 border-gray-300 rounded-lg mb-2 bg-gray-200" required autocomplete="current-password">
                            @error('password')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button class="w-4/5 ml-6 bg-rose-500 text-white py-3 rounded-lg shadow-md my-6">ワークスペースを追加</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>
