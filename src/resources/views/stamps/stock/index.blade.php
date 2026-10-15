@extends('layouts.adminlte')

@section('title', '在庫一覧')

@section('content_header')
<div class="d-flex justify-content-between align-items-start pr-2">
    <div class="col-md-4 col-sm-6 p-0">
        <div class="small-box bg-info">
            <div class="inner">
                <h5>金券在庫合計金額</h5>
                <h3 class="text-right">¥{{ number_format($totalAmount) }}</h3>
            </div>
            <div class="icon"><i class="fa-solid fa-receipt"></i></div>
        </div>
    </div>
    <div>
        <a href="{{ route('stamps.transactions.create') }}" class="btn btn-main btn-sm shadow-sm px-3 mr-2">
            <i class="fas fa-plus mr-1"></i> 入出庫登録
        </a>
        <a href="{{ route('stamps.transactions.index') }}" class="btn btn-main btn-sm shadow-sm px-3 mr-2">
            <i class="fa-solid fa-clock-rotate-left mr-1"></i> 履歴一覧
        </a>
        <a href="{{ route('stamps.audits.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fa-solid fa-money-check mr-1"></i> 棚卸監査
        </a>
    </div>
</div>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="card search-card">
    <div class="card-header">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>品目別在庫
        </h5>
    </div>
    <div class="card-body table-responsive-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>区分</th>
                    <th>品目</th>
                    <th class="text-right">額面</th>
                    <th class="text-right">現在庫</th>
                    <th class="text-right">在庫金額</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td>{{ $item->category_label }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">¥{{ number_format($item->face_value) }}</td>
                    <td class="text-right {{ $item->stock <= 0 ? 'text-danger font-weight-bold' : '' }}">
                        {{ number_format($item->stock) }}
                    </td>
                    <td class="text-right">¥{{ number_format($item->stock_amount) }}</td>
                    <td class="text-right">
                        <a href="{{ route('stamps.transactions.index', ['stamp_item_id' => $item->id]) }}"
                            class="btn btn-xs btn-outline-secondary">履歴</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">品目が登録されていません</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="4" class="text-right">合計</td>
                    <td class="text-right">¥{{ number_format($totalAmount) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop