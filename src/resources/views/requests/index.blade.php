@extends('layouts.adminlte')

@section('title', '稟議一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <a href="{{ route('requests.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規作成
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- 検索パネル --}}
    <div class="card search-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('requests.index', ['mode' => 'all']) }}" id="search-form">
                {{-- キーワード --}}
                <div class="form-group mb-3">
                    <label class="field-label">全文検索</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control form-control-sm" placeholder="文書番号・タイトル・内容で検索">
                </div>

                <div class="form-row">
                    {{-- 状態 --}}
                    <div class="form-group col-md-4">
                        <label class="field-label">状態</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>下書き</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>申請中</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>承認済</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>差戻し</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>取下げ</option>
                        </select>
                    </div>

                    {{-- 申請期間 --}}
                    <div class="form-group col-md-8">
                        <label class="field-label">申請期間</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-control datepicker text-center" placeholder="開始日">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                            </div>
                            <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-control datepicker text-center" placeholder="終了日">
                        </div>
                    </div>
                </div>

                {{-- アクション --}}
                <div class="action-bar d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('requests.index', ['mode' => 'all']) }}" class="btn-reset btn btn-sm shadow-sm">
                        <i class="fas fa-undo mr-1"></i> 条件をリセット
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                        <i class="fas fa-search mr-2"></i> この条件で絞り込む
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ステータスタブ --}}
    <div class="card card-outline search-card">
        <div class="card-header">
            <h5 class="m-0 h6 font-weight-bold text-success">
                <i class="fas fa-list-ul mr-1"></i> 稟議書一覧 ({{ number_format($requests->total()) }} 件)
            </h5>
        </div>

        @php
        $mode = request('mode', 'all');
        @endphp

        <ul class="nav nav-tabs pl-2">
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'all' ? 'active' : '' }}" href="{{ route('requests.index', array_merge(request()->except('page'), ['mode' => 'all'])) }}">
                    全申請
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'mine' ? 'active' : '' }}" href="{{ route('requests.index', array_merge(request()->except('page'), ['mode' => 'mine'])) }}">
                    自分の申請
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'approve' ? 'active' : '' }}" href="{{ route('requests.index', array_merge(request()->except('page'), ['mode' => 'approve'])) }}">
                    承認待ち
                    @if($approveCount > 0)
                    <span class="badge badge-pill badge-danger ml-1">
                        {{ $approveCount }}
                    </span>
                    @endif
                </a>
            </li>
        </ul>

        {{-- 一覧 --}}
        <div class="card-body table-responsive p-0">
            <div class="table-responsive-custom" style="max-height: 60vh;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">作成日</th>
                            <th>申請者</th>
                            <th style="width: 150px;">文書番号</th>
                            <th style="width: 100px;">文書種類</th>
                            <th>表題</th>
                            <th style="width: 100px">申請日</th>
                            <th class="text-center" style="width: 100px;">状態</th>
                            <th style="width: 150px;">現在承認者</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td class="font-weight-bold text-secondary">{{ $request->document_no }}</td>
                            <td>{{ optional($request->ringiType)->name }}</td>
                            <td>{{ $request->title }}</td>
                            <td>{{ optional($request->applied_date)->format('Y-m-d') }}</td>
                            <td class="text-center">
                                @switch($request->status)
                                @case('draft')
                                <span class="badge badge-secondary">下書き</span>
                                @break
                                @case('pending')
                                <span class="badge badge-warning">申請中</span>
                                @break
                                @case('approved')
                                <span class="badge badge-success">承認済</span>
                                @break
                                @case('returned')
                                <span class="badge badge-danger">差戻し</span>
                                @break
                                @case('withdrawn')
                                <span class="badge badge-dark">取下げ</span>
                                @break
                                @default
                                <span class="badge badge-light">{{ $request->status }}</span>
                                @endswitch

                                @if($request->can_approve)
                                <span class="badge badge-danger">承認待ち</span>
                                @endif
                            </td>
                            <td>
                                @php
                                $currentStep = $request->steps->where('step_order', $request->current_step)->first();
                                @endphp
                                {{ optional($currentStep?->approver)->name ?? '-' }}
                            </td>
                            <td class="text-center" style="width: 0 !important; white-space: nowrap;">
                                {{-- 詳細 --}}
                                <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm btn-light px-2">
                                    <i class="fas fa-info"></i>
                                </a>

                                {{-- 編集 --}}
                                @if(in_array($request->status, [\App\Models\Request\RingiRequest::STATUS_DRAFT, \App\Models\Request\RingiRequest::STATUS_RETURNED]))
                                <a href="{{ route('requests.edit', $request->id) }}" class="btn btn-sm btn-light" style="padding-left: 0.5em;padding-right:0.2em;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                {{-- PDF --}}
                                <a href="{{ route('requests.pdf', $request->id) }}" class="btn btn-sm btn-light px-2" data-fancybox data-type="pdf">
                                    <i class="far fa-file-pdf" style="color:#E30000"></i>
                                </a>

                                {{-- 削除 --}}
                                @if($request->status === \App\Models\Request\RingiRequest::STATUS_DRAFT)
                                <form action="{{ route('requests.destroy', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-light px-2">
                                        <i class="fas fa-trash-alt" style="color:#343a40"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">データがありません</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページャ --}}
            <div class="card-footer clearfix">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection