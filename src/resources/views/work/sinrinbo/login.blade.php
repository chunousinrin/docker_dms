@extends('layouts.adminlte')

@section('title', 'ログイン | 森林情報検索システム')

{{-- サイドメニューをこの画面だけ非表示にしたい場合は以下を有効に（お好みで） --}}
{{-- @section('left-sidebar') @stop --}}

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="card shadow-sm" style="width: 100%; max-width: 450px; border-top: 5px solid #2d5a27;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="fas fa-lock text-success fa-3x mb-3"></i>
                <h4 class="font-weight-bold">認証が必要です</h4>
                <p class="text-muted small">この機能を利用するにはパスワードを入力してください</p>
            </div>

            @if(session('error'))
            <div class="alert alert-danger py-2 small text-center">{{ session('error') }}</div>
            @endif

            <form action="{{ route('sinrinbo.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="small font-weight-bold text-muted">アクセスパスワード</label>
                    <input type="password" name="password" class="form-control text-center form-control-lg" placeholder="••••••••" required autofocus style="letter-spacing: 0.2em;">
                </div>
                <button type="submit" class="btn btn-success btn-block font-weight-bold py-2">
                    認証して進む <i class="fas fa-sign-in-alt ml-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@stop