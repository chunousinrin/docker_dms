@extends('layouts.adminlte')

@section('title', '発注一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規作成
    </a>
</div>

@stop

@section('content')
{{-- 検索条件カード --}}
<div class="card search-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('purchase-orders.index') }}">
            <div class="form-row">

                <div class="form-group col-md-5">
                    <label class="field-label">キーワード</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control form-control-sm" placeholder="発注番号・発注先・品名・備考">
                </div>

                <div class="form-group col-md-5">
                    <label class="field-label">発注日（期間）</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="order_date_from" value="{{ request('order_date_from') }}" class="form-control datepicker text-center" placeholder="始期">

                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                        </div>

                        <input type="text" name="order_date_to" value="{{ request('order_date_to') }}" class="form-control datepicker text-center" placeholder="終期">
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <label class="field-label">担当者</label>
                    <select name="user_id" class="form-control form-control-sm">
                        <option value="">すべて</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(request('user_id')==$user->id)>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="action-bar d-flex justify-content-between align-items-center">
                <a href="{{ route('purchase-orders.index') }}" class="btn-reset btn btn-sm shadow-sm">
                    <i class="fas fa-undo-alt mr-1"></i> 条件をリセット
                </a>
                <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm shadow-sm">
                    <i class="fas fa-search mr-2"></i> この条件で絞り込む
                </button>
            </div>

        </form>
    </div>
</div>

{{-- 一覧テーブルカード --}}
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i> 発注書一覧 ({{ $purchaseOrders->total() }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width:100px;">発注日</th>
                        <th style="width:140px;">発注番号</th>
                        <th style="width:120px;">担当者</th>
                        <th>発注先</th>
                        <th>内容</th>
                        <th style="width:140px;">金額(税込)</th>
                        <th style="width:140px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $purchaseOrder)
                    <tr>
                        <td class="text-center"> {{ optional($purchaseOrder->order_date)->format('Y-m-d') }} </td>
                        <td class="font-weight-bold text-secondary"> {{ $purchaseOrder->order_number }} </td>
                        <td> {{ $purchaseOrder->user->name }} </td>
                        <td> {{ $purchaseOrder->supplier_name }} </td>
                        <td>{{ $purchaseOrder->summary_item }}</td>
                        <td class="text-right font-weight-bold }}">
                            {{ number_format($purchaseOrder->total_amount) }} 円
                        </td>
                        <td class="text-center" style="width: 0!important;white-space: nowrap;">
                            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-light btn-sm shadow-sm" title="編集">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-light btn-sm shadow-sm" title="PDF" data-fancybox data-type="iframe">
                                <i class="far fa-file-pdf" style="color:#E30000"></i>
                            </a>
                            <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm shadow-sm" title="削除" onclick="return confirm('発注書を削除しますか？')">
                                    <i class="fas fa-trash-alt" style="color:#343a40"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            データがありません。
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $purchaseOrders->appends(request()->query())->links() }}
    </div>
</div>
@endsection