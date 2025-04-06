<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Survey</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container">
        @if ($scores->count())
            <table class="min-w-full bg-white border border-gray-200 m-10">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b"></th>
                        <th class="py-2 px-4 border-b">期待度平均</th>
                        <th class="py-2 px-4 border-b">満足度平均</th>
                        <th class="py-2 px-4 border-b">期待度ー満足度</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scores as $score)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $score->category->name }}</td>
                            <td class="py-2 px-4 border-b">{{ number_format($score->avg_expectation, 2) }}</td>
                            <td class="py-2 px-4 border-b">{{ number_format($score->avg_satisfaction, 2) }}</td>
                            <td class="py-2 px-4 border-b">{{ number_format($score->expectation_gap, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No scores available.</p>
        @endif
    </div>
</body>
</html>