@extends('layouts.adminlte')

@section('title', '見積書一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('estimates.create', ['mode' => 'new']) }}" class="btn btn-sm btn-main btn-sm shadow-sm mr-2 px-3">
        <i class="fas fa-plus mr-1"></i> 新規作成
    </a>
    <a href="{{ route('estimate2.create') }}" class="btn btn-sm btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 設計用作成
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- 検索パネル --}}
    <div class="card search-card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('estimates.index') }}" method="GET" id="search-form">
                {{-- ① キーワード --}}
                <div class="form-group mb-3">
                    <label class="field-label">キーワード検索</label>
                    <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}" placeholder="見積番号・顧客名・事業名を入力...">
                </div>

                {{-- ②〜⑤ 横一列 --}}
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="field-label">事業分類</label>
                        <select name="classification_id" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @foreach($classifications as $c)
                            <option value="{{ $c->id }}" {{ request('classification_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="field-label">作成者</label>
                        <select name="creator_id" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('creator_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="field-label">作成日（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="created_from" class="form-control datepicker text-center" value="{{ request('created_from') }}" placeholder="始期">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                            </div>
                            <input type="text" name="created_to" class="form-control datepicker text-center" value="{{ request('created_to') }}" placeholder="終期">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="field-label">実施予定日（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="scheduled_from" class="form-control datepicker text-center" value="{{ request('scheduled_from') }}" placeholder="始期">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                            </div>
                            <input type="text" name="scheduled_to" class="form-control datepicker text-center" value="{{ request('scheduled_to') }}" placeholder="終期">
                        </div>
                    </div>
                </div>

                {{-- アクションバー --}}
                <div class="action-bar d-flex justify-content-between align-items-center">
                    <a href="{{ route('estimates.index') }}" class="btn-reset btn btn-sm shadow-sm">
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
                <i class="fas fa-list-ul mr-1"></i> 見積書一覧 ({{ Illuminate\Support\Number::format($estimates->total()) }} 件)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive-custom" style="max-height: 60vh;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">作成日</th>
                            <th style="width: 150px;">見積番号</th>
                            <th style="width:80px;">区分</th>
                            <th style="width: 150px;">作成者</th>
                            <th>顧客名</th>
                            <th>件名/業務名</th>
                            <th class="text-right" style="width: 160px;">見積金額</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimates as $estimate)
                        <tr>
                            <td>{{ $estimate->created_date?->format('Y-m-d') }}</td>
                            <td>{{ $estimate->estimate_number }}</td>
                            <td>
                                @if($estimate->type === 'special')
                                <span class="badge badge-info">設計用</span>
                                @else
                                <span class="badge badge-secondary">標準</span>
                                @endif
                            </td>
                            <td>{{ $estimate->creator?->name }}</td> {{-- 追加 --}}
                            <td>{{ $estimate->customer_name }}</td>
                            <td>{{ $estimate->project_name }}</td>
                            <td class="text-right font-weight-bold">
                                {{ Illuminate\Support\Number::format(floor($estimate->total_estimate ?? 0)) }}&nbsp;円
                            </td>
                            <td class="text-center" style="width: 0!important;white-space: nowrap;">
                                {{-- 請求書作成 --}}
                                <a href="{{ route('invoices.create-from-estimate', $estimate) }}"
                                    class="btn btn-sm btn-light shadow-sm font-weight-bold">
                                    請
                                </a>

                                @if($estimate->type === 'special')
                                {{-- 設計用 --}}
                                <a href="{{ route('estimate2.showPdf', ['id' => $estimate->delete_key]) }}" class="btn btn-sm btn-action btn-light btn-pdf px-2 shadow-sm" target="_blank"" data-fancybox=" pdf-viewer" data-type="pdf">
                                    <i class="far fa-file-pdf" style="color:#E30000"></i>
                                </a>
                                @else
                                {{-- 標準 --}}
                                <a href="{{ route('estimates.pdf', $estimate) }}" class="btn btn-sm btn-light action btn-pdf px-2 shadow-sm" data-fancybox="pdf-viewer" data-type="pdf">
                                    <i class="far fa-file-pdf" style="color:#E30000"></i>
                                </a>
                                @endif

                                {{-- 削除 --}}
                                <form action="{{ route('estimates.destroy', $estimate) }}" method="POST" onsubmit="return confirm('この見積書を削除しますか？')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light px-2 shadow-sm">
                                        <i class="fas fa-trash-alt" style="color:#343a40"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ページャ --}}
        <div class="card-footer clearfix">
            {{ $estimates->links() }}
        </div>
    </div>
</div>
@endsection