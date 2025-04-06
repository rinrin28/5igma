<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
        <div class="p-8 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">ログアウト</h2>
            <p class="mb-4">ログアウトしますか？</p>
            <form method="POST" action="{{ route('logout.confirm') }}">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    ログアウト
                </button>
            </form>
        </div>
    </div>
</x-app-layout> 