@extends('layouts.adminlte')

@section('title', '地区マスタ')

@section('content_header')
<h1>地区マスタ</h1>
@endsection

@section('content')
@if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="card">
    <div class="card-header"><strong>新規登録</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('master.districts.store') }}" class="form-row">
            @csrf
            <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="地区名" required></div>
            <div class="col-md-3"><input type="text" name="code" class="form-control" placeholder="地区コード（任意）"></div>
            <div class="col-md-2"><input type="number" name="sort_order" class="form-control" placeholder="表示順" value="0"></div>
            <div class="col-md-3"><button class="btn btn-primary btn-block">登録</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>表示順</th>
                    <th>地区名</th>
                    <th>コード</th>
                    <th>字数</th>
                    <th>組合員数</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($districts as $d)
                <tr>
                    <td colspan="5" class="p-0">
                        <form method="POST" action="{{ route('master.districts.update', $d) }}" class="form-row m-0 align-items-center">
                            @csrf @method('PUT')
                            <div class="col" style="max-width:100px"><input type="number" name="sort_order" value="{{ $d->sort_order }}" class="form-control form-control-sm"></div>
                            <div class="col"><input type="text" name="name" value="{{ $d->name }}" class="form-control form-control-sm" required></div>
                            <div class="col" style="max-width:150px"><input type="text" name="code" value="{{ $d->code }}" class="form-control form-control-sm"></div>
                            <div class="col-auto">字数: {{ $d->sections_count }}</div>
                            <div class="col-auto">組合員数: {{ $d->members_count }}</div>
                            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary">更新</button></div>
                        </form>
                    </td>
                    <td style="width:80px">
                        <form method="POST" action="{{ route('master.districts.destroy', $d) }}" onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('master.sections.index') }}" class="btn btn-light">字マスタの管理へ</a>
    </div>
</div>
@endsection