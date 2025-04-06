<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h1>管理職ダッシュボード</h1>

    <!-- アンケート送信フォーム -->
    <form action="{{ route('survey.send') }}" method="POST">
        @csrf
        <button type="submit">アンケートを送信する</button>
    </form>

</body>
</html>


