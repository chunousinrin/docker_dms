@extends('layouts.adminlte')

@section('title', '電子帳簿保存一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <button class="btn btn-main btn-sm shadow-sm px-3" data-src="{{ route('transactions.create') }}" data-fancybox data-type="iframe">
        <i class="fas fa-plus mr-1"></i> 新規登録
    </button>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- 検索パネル --}}
    <div class="card search-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('transactions.index') }}" id="search-form">

                {{-- ① 取引先名（キーワード） --}}
                <div class="form-group mb-3">
                    <label class="field-label">取引先名検索</label>
                    <input type="text" name="customer_name" value="{{ request('customer_name') }}"
                        class="form-control form-control-sm" placeholder="取引先名を入力">
                </div>

                {{-- ② 取引年月日・金額の範囲 --}}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="field-label">取引年月日（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-control datepicker text-center" placeholder="始期">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                            </div>
                            <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-control datepicker text-center" placeholder="終期">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="field-label">取引金額（範囲）</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" class="form-control text-right" placeholder="最小金額">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                            </div>
                            <input type="number" name="amount_max" value="{{ request('amount_max') }}" class="form-control text-right" placeholder="最大金額">
                        </div>
                    </div>
                </div>

                {{-- アクションバー --}}
                <div class="action-bar d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn-reset">
                        <i class="fas fa-undo mr-1"></i> 条件をリセット
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                        <i class="fas fa-search mr-2"></i> この条件で絞り込む
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 一覧テーブル --}}
    <div class="card search-card mb-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <h5 class="m-0 h6 font-weight-bold text-success">
                <i class="fas fa-list-ul mr-1"></i> 電子帳簿保存一覧 ({{ number_format($transactions->total()) }} 件)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive-custom" style="max-height: 60vh;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">取引年月日</th>
                            <th>取引先名</th>
                            <th class="text-right" style="width: 150px;">取引金額</th>
                            <th>関連書類</th>
                            <th>登録者</th> {{-- 追加 --}}
                            <th class="text-center" style="width: 150px;">登録日</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr>
                            <td class="text-center">{{ $t->transaction_date }}</td>
                            <td class="font-weight-bold text-secondary">{{ $t->customer_name }}</td>
                            <td class="text-right">{{ number_format($t->amount) }} 円</td>
                            <td>
                                @foreach($t->files as $file)

                                <span class="badge badge-info" style="font-size: 0.7rem;">{{ $file->file_type }}</span>

                                @endforeach

                            </td>
                            <td>{{ $t->user->name ?? '不明' }}</td> {{-- 追加 --}}
                            <td class="text-center">{{ $t->created_at->format('Y-m-d') }}</td>
                            <td class="text-center" style="width: 0!important;white-space: nowrap;">
                                {{-- 詳細ボタン --}}
                                <button type="button"
                                    class="btn btn-sm btn-light px-2 shadow-sm"
                                    data-src="{{ route('transactions.show', $t->id) }}"
                                    data-fancybox
                                    data-type="iframe"> <i class=" fas fa-info"></i>
                                </button>

                                {{-- 削除ボタン（請求書一覧のデザインに準拠） --}}
                                <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('取引先: {{ $t->customer_name }}\n金額: ￥{{ number_format($t->amount) }}\nこのデータを削除してもよろしいですか?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light px-2 shadow-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">該当するデータは見つかりませんでした。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ページャ --}}
        <div class="card-footer clearfix">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection