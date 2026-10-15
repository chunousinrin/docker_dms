@extends('layouts.adminlte')

@section('title', '案件一覧')

@push('css')
<style>
</style>
@endpush

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('projects.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規作成
    </a>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- 検索パネル --}}
    <div class="card search-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('projects.index') }}" id="search-form">

                {{-- ① キーワード検索 --}}
                <div class="form-group mb-3">
                    <label class="field-label">キーワード検索</label>
                    <input type="text" name="keyword" class="form-control form-control-sm"
                        placeholder="現場名 / 発注者 / 内容 など" value="{{ request('keyword') }}">
                </div>

                {{-- ② セレクト・入力項目 --}}
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="field-label">担当者</label>
                        <select name="staff_name" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @foreach($staffList as $staff)
                            <option value="{{ $staff }}" {{ request('staff_name') == $staff ? 'selected' : '' }}>
                                {{ $staff }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="field-label">施工班</label>

                        <select name="group_id" class="form-control form-control-sm">
                            <option value="">すべて</option>

                            @foreach($teams as $team)
                            <option value="{{ $team->id }}"
                                {{ request('group_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="field-label">指示書種類</label>
                        <select name="order_type" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            <option value="一般" {{ request('order_type') == '一般' ? 'selected' : '' }}>一般</option>
                            <option value="保育" {{ request('order_type') == '保育' ? 'selected' : '' }}>保育</option>
                            <option value="林産" {{ request('order_type') == '林産' ? 'selected' : '' }}>林産</option>
                        </select>
                    </div>
                    <div class="form-group form-group-sm col-md-3">
                        <label class="field-label">業務種類</label>
                        <select name="business_type" class="select2 form-control">
                            <option value="">選択または入力...</option>
                            @php
                            $types = ["ライフライン", "岐阜県", "関市", "美濃市", "作業道", "利用間伐", "保育間伐", "環境税間伐", "脱炭素", "花粉症", "植栽", "里山林", "一般事業", "三川公社", "森林公社", "公団", "林道補修", "木質バイオマス", "保安林改良"];
                            @endphp
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('business_type', request('business_type')) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ③ 日付範囲指定（3列並び） --}}
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="field-label">指示日（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="ordered_from" class="form-control datepicker text-center" value="{{ request('ordered_from') }}" placeholder="始期">
                            <div class="input-group-append"><span class="input-group-text bg-white border-left-0 border-right-0">～</span></div>
                            <input type="text" name="ordered_to" class="form-control datepicker text-center" value="{{ request('ordered_to') }}" placeholder="終期">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="field-label">工期開始（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="started_from" class="form-control datepicker text-center" value="{{ request('started_from') }}" placeholder="始期">
                            <div class="input-group-append"><span class="input-group-text bg-white border-left-0 border-right-0">～</span></div>
                            <input type="text" name="started_to" class="form-control datepicker text-center" value="{{ request('started_to') }}" placeholder="終期">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="field-label">工期終了（期間）</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="completed_from" class="form-control datepicker text-center" value="{{ request('completed_from') }}" placeholder="始期">
                            <div class="input-group-append"><span class="input-group-text bg-white border-left-0 border-right-0">～</span></div>
                            <input type="text" name="completed_to" class="form-control datepicker text-center" value="{{ request('completed_to') }}" placeholder="終期">
                        </div>
                    </div>
                </div>

                {{-- アクションバー --}}
                <div class="action-bar d-flex justify-content-between align-items-center">
                    <a href="{{ route('projects.index') }}" class="btn-reset">
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
                <i class="fas fa-list-ul mr-1"></i> 案件一覧 ({{ number_format($projects->total()) }} 件)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive-custom" style="max-height: 60vh;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th style="width: 80px;">種類</th>
                            <th style="width: 120px;">業務種類</th>
                            <th style="width: 150px;">所有者</th>
                            <th style="width: 120px;">施工班</th>
                            <th style="width: 200px;">工期</th>
                            <th>事業名</th>
                            <th style="width: 120px;">指示書番号</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td class="text-center text-muted small">{{ $project->id }}</td>
                            <td><span class="badge badge-light border">{{ $project->order_type }}</span></td>
                            <td>{{ $project->business_type }}</td>
                            <td>{{ $project->owner_name }}</td>
                            <td>{{ $project->team_name }}</td>
                            <td class="small">
                                @if(!empty($project->started_on) && !empty($project->completed_on))
                                {{ $project->started_on }} <span class="text-muted">～</span> {{ $project->completed_on }}
                                @elseif(!empty($project->started_on))
                                {{ $project->started_on }} <span class="text-muted">～</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">{{ $project->project_name }}</td>
                            <td>{{ $project->order_no ?? '未作成' }}</td>
                            <td class="text-center" style="width: 0!important;white-space: nowrap;">
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm shadow-sm btn-light" style="padding-left:0.8em;padding-right:0.8em;">
                                    <i class="fas fa-info"></i>
                                </a>
                                <a data-fancybox data-type="iframe" href="{{ route('projects.pdf', $project) }}" class="btn btn-sm btn-light px-2 shadow-sm">
                                    <i class="far fa-file-pdf" style="color:#E30000;"></i>
                                </a>

                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('案件を削除してもよろしいですか？');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm shadow-sm btn-light px-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">該当する案件は見つかりませんでした。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ページャ --}}
        <div class="card-footer clearfix">
            {{ $projects->links() }}
        </div>
    </div>
</div>

@endsection