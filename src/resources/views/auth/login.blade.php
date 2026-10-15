<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中濃森林組合 - DMS -</title>
    <style>
        .company_name {
            width: 100%;
            margin: 0 auto;
            max-width: 180px;
            font-size: 1.3rem;
            text-align-last: justify;
        }

        .bg-image {
            position: absolute;
            z-index: -1;
            bottom: 0;
            right: 0;
            background-image: url("{{ asset('img/tree.svg') }}");
            background-size: 100%;
            background-position: contain;
            background-repeat: no-repeat;
            height: 100%;
            width: 100%;
            /*filter: drop-shadow(4px 4px 3px rgba(58, 58, 58, 0.7));*/
        }

        .logo {
            width: 100%;
            max-width: 180px;
            height: 100%;
        }
    </style>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        crossorigin="anonymous">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100" style="z-index: 1;">
        <div class="card shadow-lg text-center p-4" style="width: 100%; max-width:400px">
            <div class="card-body">
                <!-- ロゴ画像 -->
                <img src="{{ asset('img/jforest.svg') }}" alt="組合ロゴ" class="logo mb-1">

                <!-- 組合名 -->
                <h4 class="mb-4 company_name">中濃森林組合</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- メールアドレス --}}
                    <div class="form-group">
                        <input type="email"
                            name="email"
                            class="form-control"
                            placeholder="メールアドレス"
                            value="{{ old('email') }}"
                            required autofocus>
                    </div>

                    {{-- パスワード --}}
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="パスワード" required>
                    </div>

                    {{-- 保存チェックボックス --}}
                    <div class="form-group form-check text-center">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label class="form-check-label" for="remember">
                            ログイン情報を保存する
                        </label>
                    </div>

                    {{-- ログインボタン --}}
                    <button type="submit" class="btn btn-success btn-block">ログイン</button>

                    {{-- パスワードを忘れた場合 --}}
                    @if (Route::has('password.request'))
                    <div class="text-center mt-2">
                        <a class="small" href="{{ route('password.request') }}">
                            パスワードを忘れた場合はこちら
                        </a>
                    </div>
                    @endif
                </form>
                {{-- エラーメッセージ --}}
                @if ($errors->any())
                <div class="alert alert-warning py-2 px-3 mt-3 small" role="alert">
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-image"></div>
</body>

</html>