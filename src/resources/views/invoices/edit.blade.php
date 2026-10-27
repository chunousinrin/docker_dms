@extends('layouts.adminlte')

@section('title', '請求書詳細・編集')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>請求書詳細・編集</h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 請求書一覧
    </a>
</div>
@stop

@section('content')
<form action="{{ route('invoices.update', $invoice) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- 左側：請求書基本情報（編集不可項目メイン） --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice text-primary mr-1"></i> 請求基本情報
                    </h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">請求番号</label>
                            <div class="form-control">{{ $invoice->invoice_number }}</div>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">発行日</label>
                            <div class="form-control">{{ $invoice->billing_date ?? '-' }}
                                @if($invoice->show_billing_date !== 1)
                                (非表示)
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">支払期限</label>
                            <div class="form-control">{{ $invoice->due_date ?? '発行日より45日以内' }}</div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">顧客名</label>
                            <div class="input-group">
                                <div class="form-control">{{ $invoice->customer_name }} {{ $invoice->honorific }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業分類</label>
                            <div class="form-control">{{ $invoice->classification->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業名 / 件名</label>
                            <div class="form-control">{{ $invoice->project_name }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">場所 (Project Location)</label>
                            <div class="form-control">{{ $invoice->project_location }}</div>
                        </div>
                        <div class="col-md-6 form-group ">
                            <label class="font-weight-bold text-dark mb-1 small">工事・事業期間</label>
                            <div class="input-group">
                                <div class="form-control">{{ $invoice->completion_start_date ?? '-' }}</div>
                                <div class="input-group-append"><span class="input-group-text">～</span></div>
                                <div class="form-control border-left-0">{{ $invoice->completion_start_end ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">摘要</label>
                            <textarea class="form-control bg-white" disabled rows="3">{{ $invoice->remarks }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 明細プレビュー（完全編集不可・確認用） --}}
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i> 請求明細プレビュー
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive-custom">
                        <table class="table-custom">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="text-center py-1" style="max-width: 60px;"></th>
                                    <th class="py-1">摘要</th>
                                    <th class=" text-right py-1" style="max-width: 80px;">数量</th>
                                    <th class="text-center py-1" style="max-width: 60px;">単位</th>
                                    <th class="text-right py-1" style="max-width: 120px;">単価</th>
                                    <th class="text-right py-1" style="max-width: 140px;">金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items as $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $item->order_number }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ $item->unit }}</td>
                                    <td class="text-right">{{ number_format($item->unit_price) }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->quantity * $item->unit_price) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">明細データはありません。</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">消費税額:</td>
                                    <td class="text-right font-weight-bold">¥{{ number_format($invoice->tax_amount) }}</td>
                                </tr>
                                <tr style="font-size: 1rem; background-color: #f0f7f0;">
                                    <td colspan="5" class="text-right font-weight-bold text-success">合計金額（税込）:</td>
                                    <td class="text-right font-weight-bold text-success">¥{{ number_format($invoice->total_billing) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 右側：ステータス管理・メモ（編集可能項目メイン） --}}
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"> <i class="fas fa-edit mr-1 text-info"></i> 情報更新パネル
                    </h3>
                </div>
                <div class="card-body">
                    {{-- ★ 編集可能項目1: project_id (新システム連携用) --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small d-flex align-items-start">指示書選択 <span class="badge badge-success ml-1">編集可</span></label>
                        <select name="project_id" class="form-control select2 border border-info">
                            <option value="">-- プロジェクトを選択（未紐付け） --</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $invoice->project_id) == $project->id ? 'selected' : '' }}>
                                【{{ $project->order_no }}】{{ $project->project_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ★ 編集可能項目2: payment_date --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small d-flex align-items-start">入金日 <span class="badge badge-success ml-1">編集可</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border border-info"><i class="fas fa-calendar-alt text-muted"></i></span>
                            </div>
                            <input type="text" name="payment_date" value="{{ old('payment_date', $invoice->payment_date) }}"
                                class="form-control datepicker text-center font-weight-bold border border-info" placeholder="未入金 (クリックして入力)">
                        </div>
                    </div>

                    {{-- ★ 編集可能項目3: memo --}}
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1 small d-flex align-items-start">社内用メモ <span class="badge badge-success ml-1">編集可</span></label>
                        <textarea name="memo" class="form-control border border-info" rows="6"
                            placeholder="社内共有用のメモや、旧データからの引き継ぎ事項などを入力してください。">{{ old('memo', $invoice->memo) }}</textarea>
                    </div>

                    <hr>
                    {{-- ★ PDFプレビューボタンを追加 --}}
                    {{-- 修正後：Fancybox仕様 --}}
                    <a href="{{ route('invoices.pdf', $invoice->id) }}"
                        data-fancybox="invoices" data-type="iframe" class="btn btn-sm btn-secondary btn-block mb-2 shadow-sm">
                        <i class="far fa-file-pdf mr-1"></i> PDFプレビュー
                    </a>
                    {{-- 保存ボタン --}}
                    <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm">
                        <i class="fas fa-save mr-2"></i> 変更を保存
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection