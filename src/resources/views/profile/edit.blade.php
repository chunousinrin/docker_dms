@extends('layouts.adminlte')

@section('title', 'プロフィール')

@section('content')
<div class="container-fluid">

    {{-- フラッシュメッセージ --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <i class="icon fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mt-1">
            <div class="col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-user-cog mr-2 text-primary"></i>プロフィール設定
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- 基本情報 --}}
                        <div class="form-group">
                            <label for="name" class="font-weight-bold text-dark mb-1 small">
                                氏名 <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror required_field"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="font-weight-bold text-dark mb-1 small">
                                メールアドレス <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror required_field"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        {{-- パスワード変更セクション --}}
                        <div class="p-2 mb-3 bg-light rounded font-weight-bold text-secondary border-left border-secondary small mt-4">
                            <i class="fas fa-lock mr-1"></i> パスワード変更（変更する場合のみ入力）
                        </div>

                        <div class="form-group">
                            <label for="password" class="font-weight-bold text-dark mb-1 small">
                                新しいパスワード <span class="badge badge-secondary ml-1">任意</span>
                            </label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="全角半角英数字など">
                            @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold text-dark mb-1 small">
                                確認用パスワード <span class="badge badge-secondary ml-1">任意</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>

                        {{-- 印影設定セクション --}}
                        <div class="p-2 mb-3 bg-light rounded font-weight-bold text-secondary border-left border-secondary small mt-4">
                            <i class="fas fa-stamp mr-1"></i> 印影設定
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">
                                印影ファイルのアップロード <span class="badge badge-secondary ml-1">任意</span>
                            </label>
                            <div class="custom-file">
                                <input type="file" name="stamp" class="custom-file-input @error('stamp') is-invalid @enderror" id="stamp_file" accept=".svg">
                                <label class="custom-file-label" for="stamp_file" data-browse="参照">印影ファイルを選択...</label>
                                @error('stamp')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        @if($user->stamp)
                        <div class="form-group mt-3 p-3 border rounded bg-light d-inline-block">
                            <label class="font-weight-bold text-muted mb-2 d-block small">現在登録済みの印影</label>
                            <div class="bg-white p-2 rounded border text-center" style="max-width: 120px;">
                                {!! $user->stamp !!}
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">印影作成オンラインツール</label>
                            <div class="row justify-content-between">
                                <div class="col-md-4 mb-2">
                                    <a href="https://tools.konomi.app/hanko" class="btn btn-outline-secondary btn-block" target="_blank" rel="noopener noreferrer">
                                        <i class=" fas fa-link mr-1"></i>Ribbit Tools</a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="https://tools.digrart.jp/stamp-maker/" class="btn btn-outline-secondary btn-block" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-link mr-1"></i>digtools</a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="https://ogurana17.github.io/SVG-Inkan/" class="btn btn-outline-secondary btn-block" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-link mr-1"></i>印鑑画像ジェネレーター</a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tags mr-2 text-info"></i>所属・役職</h3>
                    </div>
                    <div class="card-body">
                        <span>*所属・役職等は管理者ユーザーのみ変更できます</span>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">所属</label>
                            <input class="form-control" value="{{ $user->department }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">課・係</label>
                            <input class="form-control" value="{{ $user->section }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">役職</label>
                            <input class="form-control" value="{{ $user->position }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">上長</label>
                            <input class="form-control" value="{{ optional($user->manager)->name }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">
                            <i class="fas fa-save mr-2"> </i>設定を保存する </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('js')
<script>
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'stamp_file') {
            const fileName = e.target.files[0] ? e.target.files[0].name : '印影ファイルを選択...';
            const label = e.target.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        }
    });
</script>
@endsection