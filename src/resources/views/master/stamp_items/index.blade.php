@extends('layouts.adminlte')

@section('title', '品目マスタ')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>品目マスタ </h1>
    <div>
        <a href="{{ route('master.stamp_items.create') }}" class="btn btn-sm btn-main shadow-sm px-3">
            <i class="fas fa-plus"></i> 品目を追加
        </a>
    </div>
</div>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="card search-card">
    <div class="card-body table-responsive-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>表示順</th>
                    <th>区分</th>
                    <th>品目</th>
                    <th class="text-right">額面</th>
                    <th class="text-right">現在庫</th>
                    <th>状態</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr class="{{ $item->is_active ? '' : 'text-muted' }}">
                    <td>{{ $item->sort_order }}</td>
                    <td>{{ $item->category_label }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">¥{{ number_format($item->face_value) }}</td>
                    <td class="text-right">{{ number_format($item->stock) }}</td>
                    <td>
                        <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                            {{ $item->is_active ? '有効' : '無効' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('master.stamp_items.edit', $item) }}" class="btn btn-xs btn-info">編集</a>
                        <form method="POST" action="{{ route('master.stamp_items.destroy', $item) }}" class="d-inline"
                            onsubmit="return confirm('「{{ $item->name }}」を削除しますか？');">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop