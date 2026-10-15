@extends('layouts.adminlte')

@section('title', '稟議書詳細')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>稟議書詳細</h1>

    <div>
        <a href="{{ route('requests.pdf', $ringi->id) }}" class="btn btn-sm btn-main px-3" data-fancybox data-type="pdf">
            <i class="far fa-file-pdf mr-1"></i> PDF
        </a>
        <a href="{{ route('requests.index', ['mode' => 'all']) }}" class="btn btn-sm btn-main">
            <i class="fas fa-list mr-1"></i> 稟議書一覧
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- 本文 --}}
        <div class="col-md-8">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <div class="card-title d-flex justify-content-between align-items-center w-100">
                        <span>稟議情報</span>
                        <div>
                            @switch($ringi->status)
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
                            @endswitch
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">文書番号</label>
                        <div class="form-control bg-light">
                            {{ $ringi->document_no }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">文書種類</label>
                        <div class="form-control bg-light">
                            {{ $ringi->ringiType->name ?? '' }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">申請日</label>
                        <div class="form-control bg-light">
                            {{ optional($ringi->applied_date)->format('Y-m-d') ?? '未申請' }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">申請者</label>
                        <div class="form-control bg-light">
                            {{ $ringi->user->name }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">表題</label>
                        <div class="form-control bg-light">
                            {{ $ringi->title }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">内容</label>
                        <div class="border rounded p-2 bg-light" style="min-height:250px;">
                            {!! nl2br(e($ringi->content)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 右側 --}}
        <div class="col-md-4">
            {{-- 添付ファイル --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip mr-1"></i>
                        添付ファイル
                        <span class="badge badge-info ml-1">
                            {{ $ringi->currentAttachments->count() }}
                        </span>
                    </h3>
                </div>

                <div class="card-body">
                    @forelse($ringi->currentAttachments as $file)
                    <div class="border rounded p-2 mb-2">
                        <a href="{{ Storage::url($file->file_path) }}" data-fancybox data-type="pdf">
                            <i class="fas fa-paperclip mr-1"></i> {{ $file->file_name }}
                        </a>
                    </div>
                    @empty
                    <div class="text-muted">添付ファイルなし</div>
                    @endforelse
                </div>
            </div>

            {{-- 取下げボタン --}}
            @if($ringi->user_id === auth()->id() && $ringi->status === \App\Models\Request\RingiRequest::STATUS_PENDING)
            <form method="POST" action="{{ route('requests.withdraw', $ringi->id) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-secondary btn-block shadow-sm mb-3 btn-outline-warning" style="border: none !important;" onclick="return confirm('申請を取り下げますか？')">
                    <i class="far fa-times-circle mr-1"></i> 取下げ
                </button>
            </form>
            @endif

            {{-- 承認状況 --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">承認状況</h3>
                </div>

                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>順番</th>
                                <th>承認者</th>
                                <th>状態</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ringi->steps->sortBy('step_order') as $step)
                            <tr>
                                <td>{{ $step->step_order }}</td>
                                <td>{{ $step->approver->name ?? '未設定' }}</td>
                                <td>
                                    @switch($step->status)
                                    @case('approved')
                                    <span class="badge badge-success">承認済</span>
                                    @break
                                    @case('returned')
                                    <span class="badge badge-danger">差戻し</span>
                                    @break
                                    @case('pending')
                                    <span class="badge badge-warning">承認待ち</span>
                                    @break
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">承認フロー未作成</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 承認処理 --}}
            @if($canApprove)
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">承認処理</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('requests.approve', $ringi->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block mb-1">承認</button>
                    </form>

                    <form method="POST" action="{{ route('requests.return', $ringi->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block mb-1">差戻し</button>
                        <textarea name="comment" class="form-control" rows="3" placeholder="差戻し理由を入力"></textarea>
                    </form>
                </div>
            </div>
            @endif

            {{-- 承認履歴 --}}
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">承認履歴</h3>
                </div>

                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>日時</th>
                                <th>担当者</th>
                                <th>処理</th>
                                <th>コメント</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ringi->histories->sortByDesc('approved_at') as $history)
                            <tr>
                                <td>{{ $history->approved_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $history->approver->name ?? '' }}</td>
                                <td>
                                    @if($history->action === 'approved')
                                    承認
                                    @elseif($history->action === 'returned')
                                    差戻し
                                    @endif
                                </td>
                                <td>{{ $history->comment }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">履歴なし</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .custom-file-label::after {
        content: "参照" !important;
    }

    .custom-file-label {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding-right: 85px;
    }
</style>
@endsection