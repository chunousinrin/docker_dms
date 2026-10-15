@extends('adminlte::master')

@section('title', '勤怠管理')

@section('body')
<h2 class="pl-2 py-2">作業員勤怠管理</h2>
<div class="container-fluid">

    {{-- タブカード --}}
    <div class="card search-card mb-3">
        <div class="card-header p-0">
            <ul class="nav nav-tabs nav-justified" id="attendance-tab" role="tablist">
                {{-- 検索 --}}
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold rounded-0" id="search-tab" data-toggle="pill" href="#search-area" role="tab">
                        <i class="fas fa-search"></i>&nbsp;検索
                    </a>
                </li>
                {{-- CSV --}}
                <li class="nav-item">
                    <a class="nav-link font-weight-bold rounded-0" id="csv-tab" data-toggle="pill" href="#csv-area" role="tab">
                        <i class="fas fa-file-csv"></i>&nbsp;CSV出力
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- ===================================================== --}}
                {{-- 検索タブ --}}
                {{-- ===================================================== --}}
                <div class="tab-pane fade show active" id="search-area" role="tabpanel">
                    <form method="GET" action="{{ route('attendance_v2.index') }}">
                        <div class="form-row align-items-center mb-2">
                            {{-- 日付 --}}
                            <div class="col-5">
                                <label class="field-label">日付</label>
                                <input type="input" name="work_date" class="form-control datepicker" value="{{ request('work_date', $workDate) }}">
                            </div>
                            <div class="col-5">
                                {{-- 作業員 --}}
                                <label class="field-label">作業員</label>
                                <select name="worker_id" class="form-control">
                                    <option value="">すべて</option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->id }} : {{ $worker->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- 未退勤 --}}
                            <div class="col-auto ml-auto">
                                <label class="field-label">&nbsp;</label>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="only_working" value="1" {{ request('only_working') ? 'checked' : '' }}>
                                    <label class="form-check-label">未退勤のみ</label>
                                </div>
                            </div>
                        </div>
                        {{-- 検索 --}}
                        <div class="action-bar d-flex justify-content-between align-items-center">
                            <a href="{{ route('attendance_v2.index') }}" class="btn-reset">
                                <i class="fas fa-undo mr-1"></i> 条件をリセット
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                                <i class="fas fa-search mr-2"></i> この条件で絞り込む
                            </button>
                        </div>
                    </form>
                </div>
                {{-- ===================================================== --}}
                {{-- CSVタブ --}}
                {{-- ===================================================== --}}
                <div class="tab-pane fade" id="csv-area" role="tabpanel">
                    <form method="GET" action="{{ route('attendance_v2.exportCsv') }}">
                        <div class="form-row">
                            {{-- 対象月 --}}
                            <div class="col-auto">
                                <label class="field-label">対象月</label>
                            </div>
                            <div class="col-2">
                                <input type="month" name="target_month" class="form-control" value="{{ now()->format('Y-m') }}">
                            </div>

                            {{-- CSV出力 --}}
                            <div class="col-2 ml-auto">
                                <button class="btn btn-sm btn-success btn-block"><i class="fas fa-file-csv"></i>&nbsp;CSV出力</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 集計 --}}
    <div class="row mb-3">
        {{-- 未退勤 --}}
        <div class="col-md-4">
            <div class="small-box bg-warning mb-0">
                <div class="inner">
                    <p>出勤中</p>
                    <h3 class="text-center">{{ $workingCount }}</h3>
                </div>
            </div>
        </div>
        {{-- 未打刻 --}}
        <div class="col-md-4">
            <div class="small-box bg-danger mb-0">
                <div class="inner">
                    <p>未打刻</p>
                    <h3 class="text-center">{{ $missingCount }}</h3>
                </div>
            </div>
        </div>
        {{-- 総人数 --}}
        <div class="col-md-4">
            <div class="small-box bg-info mb-0">
                <div class="inner">
                    <p>総人数</p>
                    <h3 class="text-center">{{ $totalCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 一覧 --}}
    <div class="card search-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <h5 class="m-0 h6 font-weight-bold text-success">
                <i class="fas fa-list-ul mr-1"></i>勤怠一覧
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive-custom">
                <table class="table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 180px;">氏名</th>
                            <th style="width: 180px;">所属</th>
                            <th style="width: 120px;">出勤</th>
                            <th style="width: 120px;">退勤</th>
                            <th style="width: 120px;">状態</th>
                            <th>コメント</th>
                            <th class="text-center" style="width:0;white-space:nowarap">編集</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $worker)
                        @php
                        $atd = $worker->todayAttendance;
                        $rowClass = !$atd ? 'table-danger' : '';
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td>{{ $worker->id }}</td>
                            <td>{{ $worker->name }}</td>
                            <td>{{ $atd->group->name ?? '-' }}</td>
                            <td>{{ $atd?->clock_in?->format('H:i') }}</td>
                            <td>{{ $atd?->clock_out?->format('H:i') }}</td>
                            <td> <span class="badge {{ $worker->attendanceStatusClass() }}"> {{ $worker->attendanceStatusLabel() }} </span>
                            </td>
                            <td>{{ $atd->comment ?? '' }}</td>
                            <td class="text-center">
                                @if($worker->todayAttendance)
                                <a href=" {{ route('attendance_v2.edit', $worker->todayAttendance->id ) }}" class="btn btn-light btn-sm shadow-sm" data-fancybox data-type="iframe" style="padding-left: 0.5em;padding-right:0.2em;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @else
                                <a href="{{ route('attendance_v2.createManual',['worker' => $worker->id,'work_date' => $workDate,]) }}" class="btn btn-light btn-sm px-2" data-fancybox data-type="iframe">
                                    <i class="fas fa-plus"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">データがありません</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection