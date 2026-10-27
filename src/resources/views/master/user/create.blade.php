@extends('layouts.adminlte')

@section('title', 'ユーザー編集')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('users.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> ユーザー一覧
    </a>
</div>
@endsection

@section('content')

<form method="POST"
    action="{{ route('users.store') }}">
    @csrf
    <div class="card card-outline card-primary mt-1">
        <div class="card-header">
            <h3 class="card-title font-weight-bold text-dark">
                <i class="fas fa-user-cog mr-2 text-primary"></i>ユーザー登録
            </h3>
        </div>

        <div class="card-body">

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">氏名</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                @error('name')
                <div class="invalid-feedback"> {{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">メール</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                <div class="invalid-feedback"> {{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">部署</label>
                <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}">
                @error('department')
                <div class="invalid-feedback"> {{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">課</label>
                <input type="text" name="section" class="form-control @error('section') is-invalid @enderror" value="{{ old('section') }}">
                @error('section')
                <div class="invalid-feedback"> {{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">役職</label>
                <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}">
                @error('position')
                <div class="invalid-feedback"> {{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">上司</label>

                <select name="manager_id" class="form-control">
                    <option value="">なし</option>

                    @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('manager_id')==$manager->id)>
                        {{ $manager->display_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <input name="auth_type" type="hidden" value="1">
            <input name="used" type="hidden" value="1">

            <div class="form-group">
                <div class="alert alert-info mb-0">
                    初期パスワードは登録時に自動生成されます。<br>
                    登録完了後の画面で表示されるパスワードを利用者へ通知してください。
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button class="btn btn-sm btn-primary btn-block">
                <i class="fas fa-save mr-2"> </i>ユーザー登録する
            </button>
        </div>

    </div>

</form>
@endsection