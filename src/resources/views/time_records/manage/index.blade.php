{{-- resources/views/time_records/manage/index.blade.php --}}
@extends('layouts.adminlte')

@section('title', '勤怠管理')

@section('content_header')
@endsection


@section('css')
<style>
    /* 一覧画面(ブラウザ表示)側の曜日色分け */
    .table-saturday td {
        color: #1565c0;
    }

    .table-sunday td {
        color: #c62828;
    }
</style>
@endsection

@section('content')

{{-- 検索フォーム --}}
<div class="card search-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('time_records.manage.index') }}">

            <!-- 親の row に align-items-end を指定して高さを揃え、下部に寄せやすくする -->
            <div class="form-row align-items-end">

                <!-- 左側：検索条件（従業員・出勤日） -->
                <div class="form-group col-md-6 mb-0">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-0">
                            <label class="field-label">従業員</label>
                            <select name="user_id" id="user_id" class="form-control form-control-sm">
                                <option value="">全て</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-8 mb-0">
                            <label class="field-label">出勤日</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="date_from" id="date_from" class="form-control datepicker text-center" value="{{ request('date_from') }}" placeholder="始期">
                                <div class="input-group-append">
                                    <span class="input-group-text border-left-0 border-right-0">～</span>
                                </div>
                                <input type="text" name="date_to" id="date_to" class="form-control datepicker text-center" value="{{ request('date_to') }}" placeholder="終期">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 右側：ボタン群 -->
                <div class="form-group col-md-6 mb-0">
                    <!-- d-flex と justify-content-end で右寄せ、align-items-center で垂直方向の中央揃えにする場合 -->
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="{{ route('time_records.manage.index') }}" class="btn-reset btn btn-sm btn-outline-secondary shadow-sm mr-2" title="リセット">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </a>

                        <a href="{{ route('time_records.manage.export', request()->query()) }}" class="btn btn-sm btn-outline-success shadow-sm mr-2" title="CSV出力">
                            <i class="fas fa-file-csv"></i>
                        </a>

                        {{-- PDFプレビュー(Fancybox) --}}
                        <a href="{{ route('time_records.manage.pdf', request()->query()) }}"
                            class="btn btn-sm btn-outline-danger shadow-sm mr-2"
                            data-fancybox
                            data-type="iframe"
                            data-width="90%"
                            data-height="90%"
                            title="PDFプレビュー">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                            <i class="fas fa-search mr-2"></i> この条件で絞り込む
                        </button>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>

{{-- 検索結果一覧(通常のブラウザ画面) --}}
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>勤怠一覧 ({{ number_format($timeRecords->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table-custom mb-0">
            <thead class="thead-light">
                <tr>
                    <th>勤務日</th>
                    <th>従業員</th>
                    <th>勤務開始時間</th>
                    <th>時間内退勤</th>
                    <th>時間内出勤</th>
                    <th>勤務終了時間</th>
                    <th>備考</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeRecords as $record)
                @php
                $dow = optional($record->work_date)->dayOfWeek;
                $rowClass = $dow === 0 ? 'table-sunday' : ($dow === 6 ? 'table-saturday' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ optional($record->work_date)->format('Y-m-d') }}</td>
                    <td>{{ $record->user->name ?? '-' }}</td>
                    <td>{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('H:i:s') : '-' }}</td>
                    <td>{{ $record->break_start_time ? \Carbon\Carbon::parse($record->break_start_time)->format('H:i:s') : '-' }}</td>
                    <td>{{ $record->break_end_time ? \Carbon\Carbon::parse($record->break_end_time)->format('H:i:s') : '-' }}</td>
                    <td>{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('H:i:s') : '-' }}</td>
                    <td>{{ $record->note }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">該当する勤怠データがありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($timeRecords->hasPages())
    <div class="card-footer">
        {{ $timeRecords->links() }}
    </div>
    @endif
</div>

@endsection