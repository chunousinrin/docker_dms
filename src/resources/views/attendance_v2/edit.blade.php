@extends('adminlte::master')

@section('title', '勤怠修正')

@push('css')
<style>
    /* bodyだけでなく、AdminLTEのラッパー要素もすべてリセットする */
    html,
    body,
    .wrapper,
    .content-wrapper,
    .content,
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        min-height: auto !important;
        /* これが一番重要です */
        height: auto !important;
        background-color: transparent !important;
        /* 背景が2重になるのを防ぐ */
    }

    /* card自体の余白も完全にゼロにする */
    .card {
        margin: 0 !important;
        box-shadow: none !important;
        /* モーダル内なので影が不要なら */
    }
</style>
@endpush

@section('body')
<div class="card m-0">

    <div class="card-header">
        <h3 class="m-0">勤怠修正</h3>
    </div>

    <form method="POST" action="{{ route('attendance_v2.update', $attendance->id) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>作業員</label>
                <input type="text" class="form-control" value="{{ $attendance->worker->name }}" readonly>
            </div>

            <div class="form-group">
                <label>班</label>
                <select name="group_id" class="form-control">
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ $attendance->group_id == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>出勤</label>
                <input type="time" name="clock_in" class="form-control" value="{{ optional($attendance->clock_in)->format('H:i') }}">
            </div>

            <div class="form-group">
                <label>退勤</label>
                <input type="time" name="clock_out" class="form-control" value="{{ optional($attendance->clock_out)->format('H:i') }}">
            </div>

            <div class="form-group">
                <label>コメント</label>

                <textarea name="comment" class="form-control" rows="3">{{ $attendance->comment }}
                打刻忘れ　職員入力</textarea>
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-sm btn-primary px-4 shadow-sm">
                <i class="fas fa-save"></i> 保存
            </button>
        </div>
    </form>
</div>

@endsection