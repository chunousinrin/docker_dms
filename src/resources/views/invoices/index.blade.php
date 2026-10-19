@extends('layouts.adminlte')

@section('title', '請求書一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('invoices.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規作成
    </a>
</div>
@endsection

@section('content')
{{-- 検索パネル --}}
<div class="card search-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('invoices.index') }}" id="search-form">

            {{-- ① キーワード --}}
            <div class="form-group mb-3">
                <label class="field-label">キーワード検索</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    class="form-control form-control-sm" placeholder="請求番号・顧客名・事業名を入力">
            </div>

            {{-- ②〜⑤ セレクト・日付 --}}
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label class="field-label">事業分類</label>
                    <select name="category_id" class="form-control form-control-sm">
                        <option value="">すべて</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label class="field-label">作成者</label>
                    <select name="creator_id" class="form-control form-control-sm">
                        <option value="">すべて</option>
                        @foreach($creators as $user)
                        <option value="{{ $user->id }}"
                            {{ request('creator_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="field-label">発行日（期間）</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="billing_start" value="{{ request('billing_start') }}" class="form-control datepicker text-center" placeholder="始期">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                        </div>
                        <input type="text" name="billing_end" value="{{ request('billing_end') }}" class="form-control datepicker text-center" placeholder="終期">
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label class="field-label">支払日（期間）</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="payment_start" value="{{ request('payment_start') }}" class="form-control datepicker text-center" placeholder="始期">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                        </div>
                        <input type="text" name="payment_end" value="{{ request('payment_end') }}" class="form-control datepicker text-center" placeholder="終期">
                    </div>
                </div>

                <div class="form-group col-md-1 text-center">
                    <label class="field-label">未入金のみ</label>
                    <div class="custom-control custom-checkbox pt-1">
                        {{-- チェックされていたら保持する設定（request('unpaid') == 1） --}}
                        <input type="checkbox" name="unpaid" value="1" class="custom-control-input" id="checkUnpaid"
                            {{ request('unpaid') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="checkUnpaid" style="cursor: pointer;">

                        </label>
                    </div>
                </div>

                <div class="form-group col-md-1 text-center">
                    <label class="field-label" for="trashed">削除済みを表示</label>
                    <div class="custom-control custom-checkbox pt-1">
                        <input type="checkbox" class="custom-control-input" id="trashed" name="trashed" value="1"
                            {{ request('trashed') === '1' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="trashed" style="cursor: pointer;"></label>
                    </div>
                </div>
            </div>

            {{-- アクションバー --}}
            <div class="action-bar d-flex justify-content-between align-items-center">
                <a href="{{ route('invoices.index') }}" class="btn-reset btn btn-sm shadow-sm">
                    <i class="fas fa-undo-alt mr-1"></i> 条件をリセット
                </a>
                <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm shadow-sm">
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
            <i class="fas fa-list-ul mr-1"></i> 請求書一覧 ({{ number_format($invoices->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive-custom" style="max-height: 60vh;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">発行日</th>
                        <th style="width: 150px;">請求番号</th>
                        <th style="width: 120px;">作成者</th>
                        <th>顧客名</th>
                        <th>事業名</th>
                        <th class="text-right" style="width: 160px;">請求金額 / 状態</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="text-center">{{ $invoice->billing_date }}</td>
                        <td class="font-weight-bold text-secondary">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->creator->name ?? '-' }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->project_name }}</td>
                        <td class="text-right">
                            <span class="font-weight-bold mr-2">{{ number_format($invoice->total_billing) }} 円</span>
                            <span class="badge {{ $invoice->payment_date ? 'badge-success' : 'badge-warning' }} p-1" style="font-size: 0.7rem;">
                                {{ $invoice->payment_date ? '済' : '未' }}
                            </span>
                        </td>
                        <td class="text-center" style="width: 0!important;white-space: nowrap;">

                            @if($invoice->trashed())
                            <form action="{{ route('invoices.restore', $invoice) }}" method="POST" class="m-0" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light shadow-sm font-weight-bold" style="font-size:0.7rem">復元</button>
                            </form>
                            @else

                            {{-- 編集ボタン --}}
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm shadow-sm btn-light" style="padding-left: 0.5em;padding-right:0.2em;">
                                <i class="fas fa-edit" style="color:#343a40"></i>
                            </a>

                            {{-- PDFボタン --}}
                            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-sm shadow-sm btn-light px-2" data-fancybox="pdf-viewer" data-type="pdf">
                                <i class="far fa-file-pdf" style="color:#E30000"></i>
                            </a>

                            {{-- 削除ボタン --}}
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="m-0 js-delete-form" style="display: inline;"
                                data-invoice-number="{{ $invoice->invoice_number }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm shadow-sm btn-light px-2">
                                    <i class="fas fa-trash-alt" style="color:#343a40"></i>
                                </button>
                            </form>
                            <a href="{{ route('invoices.transmittal', ['customer' => $invoice->customer_name, 'honorific' => $invoice->honorific]) }}"
                                class="btn btn-sm shadow-sm btn-light font-weight-bold" title="送付文書作成">送
                            </a>

                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">該当する請求書は見つかりませんでした。</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
@push('js')
<script>
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.js-delete-form');
        if (!form) return;
        if (!confirm('請求番号: ' + form.dataset.invoiceNumber + '\n削除してもよろしいですか?')) {
            e.preventDefault();
        }
    });
</script>
@endpush