<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>保存しました</title>
</head>
<body>
    {{-- fancybox の iframe 内から親ウィンドウを一覧へ遷移させる（iframe 外で開かれた場合は通常遷移） --}}
    <script>
        window.parent.location.href = @js(route('transactions.index'));
    </script>
    <noscript><a href="{{ route('transactions.index') }}">一覧へ戻る</a></noscript>
</body>
</html>
