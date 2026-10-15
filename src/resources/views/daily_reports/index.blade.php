@extends('layouts.adminlte')

@section('title', '業務日報一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    @if ($todayReport)
    <a href="{{ route('daily-reports.edit', $todayReport) }}" class="btn btn-warning btn-sm shadow-sm px-3 font-weight-bold">
        <i class="fas fa-pen"></i> 本日の日報を編集
    </a>
    @else
    <a href="{{ route('daily-reports.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus"></i> 本日の日報を書く
    </a>
    @endif
</div>
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('info'))
<div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="card search-card mb-4">
    <div class="card-body">
        <form method="GET" class="form-row align-items-end">
            <!-- 期間入力エリア -->
            <div class="form-group col-md-4 mb-2">
                <label class="field-label">期間</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="work_date_from" class="form-control datepicker text-center" value="{{ request('work_date_from') }}" placeholder="始期">
                    <div class="input-group-append">
                        <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                    </div>
                    <input type="text" name="work_date_to" class="form-control datepicker text-center" value="{{ request('work_date_to') }}" placeholder="終期">
                </div>
            </div>

            <!-- 絞り込みボタンエリア -->
            <div class="form-group col-md-2 mb-2">
                <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                    <i class="fas fa-search mr-2"></i>絞り込み</button>
            </div>
        </form>
    </div>
</div>

<div class="card search-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i> 業務日報一覧 ({{ Illuminate\Support\Number::format($dailyReports->total()) }} 件)
        </h5>
    </div>

    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:0;" class="text-nowrap border-right px-3">勤務日</th>
                    <th colspan="3" class="border-right px-3">午前</th>
                    <th colspan="3" class="border-right px-3">午後</th>
                    <th class="border-right px-3">備考</th>
                    <th style="width:0;" class="text-nowrap text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailyReports as $dailyReport)
                <tr>
                    <td style="width:0;" class="text-nowrap border-right px-3">{{ $dailyReport->work_date->format('Y-m-d') }}</td>
                    <td style="width:0;" class="text-nowrap pl-3">
                        @if($dailyReport->amWeather)
                        @switch($dailyReport->amWeather->name)
                        @case('晴')
                        <i class="fas fa-sun text-warning" title="晴れ"></i>
                        @break
                        @case('曇')
                        <i class="fas fa-cloud text-secondary" title="曇り"></i>
                        @break
                        @case('雨')
                        <i class="fas fa-umbrella text-primary" title="雨"></i>
                        @break
                        @case('雷')
                        <i class="fas fa-bolt text-warning" title="雷"></i>
                        @break
                        @case('雪')
                        <i class="fa-regular fa-snowflake text-info" title="雪"></i>
                        @break
                        @default
                        {{ $dailyReport->amWeather->name }}
                        @endswitch
                        @else
                        -
                        @endif
                    </td>
                    <td style="width:0;" class="text-nowrap">{{ $dailyReport->amBusinessType->name ?? '-' }}</td>
                    <td style="width:0;" class="text-nowrap border-right pr-3">{{ $dailyReport->am_summary ?? '-' }}</td>
                    <td style="width:0;" class="text-nowrap pl-3">
                        @if($dailyReport->pmWeather)
                        @switch($dailyReport->pmWeather->name)
                        @case('晴')
                        <i class="fas fa-sun text-warning" title="晴れ"></i>
                        @break
                        @case('曇')
                        <i class="fas fa-cloud text-secondary" title="曇り"></i>
                        @break
                        @case('雨')
                        <i class="fas fa-umbrella text-primary" title="雨"></i>
                        @break
                        @case('雷')
                        <i class="fas fa-bolt text-warning" title="雷"></i>
                        @break
                        @case('雪')
                        <i class="fa-regular fa-snowflake text-info" title="雪"></i>
                        @break
                        @default
                        {{ $dailyReport->pmWeather->name }}
                        @endswitch
                        @else
                        -
                        @endif
                    </td>
                    <td style="width:0;" class="text-nowrap">{{ $dailyReport->pmBusinessType->name ?? '-' }}</td>
                    <td style="width:0;" class="text-nowrap border-right pr-3">{{ $dailyReport->pm_summary ?? '-' }}</td>
                    <td class="border-right px-3">{{ $dailyReport->remark ?? '-' }}</td>
                    <td style="width:0;" class="text-nowrap text-right">
                        <a href="{{ route('daily-reports.show', $dailyReport) }}" class="btn btn-sm btn-light px-2 shadow-sm" title="詳細">
                            <i class="fas fa-info"></i>
                        </a>
                        <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="btn btn-sm btn-light shadow-sm" style="padding-left: 0.5em;padding-right:0.2em;" title="編集">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('daily-reports.destroy', $dailyReport) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light px-2 shadow-sm" title="削除">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">日報がありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $dailyReports->links() }}
    </div>
</div>
@endsection