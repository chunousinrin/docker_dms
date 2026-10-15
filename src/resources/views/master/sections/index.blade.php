@extends('layouts.adminlte')

@section('title', '字マスタ')

@section('content_header')
<h1>字マスタ</h1>
@endsection

@section('content')
@if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="card">
    <div class="card-header"><strong>新規登録</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('master.sections.store') }}" class="form-row">
            @csrf
            <div class="col-md-3">
                <select name="district_id" class="form-control" required>
                    <option value="">所属地区を選択</option>
                    @foreach ($districts as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="字名" required></div>
            <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="コード（任意）"></div>
            <div class="col-md-2"><input type="number" name="sort_order" class="form-control" placeholder="表示順" value="0"></div>
            <div class="col-md-2"><button class="btn btn-primary btn-block">登録</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>所属地区</th>
                    <th>字名</th>
                    <th>コード</th>
                    <th>表示順</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sections as $s)
                <tr>
                    <td style="width:200px">{{ $s->district->name }}</td>
                    <td colspan="3" class="p-0">
                        <form method="POST" action="{{ route('master.sections.update', $s) }}" class="form-row m-0 align-items-center">
                            @csrf @method('PUT')
                            <input type="hidden" name="district_id" value="{{ $s->district_id }}">
                            <div class="col"><input type="text" name="name" value="{{ $s->name }}" class="form-control form-control-sm" required></div>
                            <div class="col" style="max-width:150px"><input type="text" name="code" value="{{ $s->code }}" class="form-control form-control-sm"></div>
                            <div class="col" style="max-width:100px"><input type="number" name="sort_order" value="{{ $s->sort_order }}" class="form-control form-control-sm"></div>
                            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary">更新</button></div>
                        </form>
                    </td>
                    <td style="width:80px">
                        <form method="POST" action="{{ route('master.sections.destroy', $s) }}" onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('master.districts.index') }}" class="btn btn-light">地区マスタの管理へ</a>
    </div>
</div>
@endsection