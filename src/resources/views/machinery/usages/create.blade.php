@extends('layouts.adminlte')

@section('title', '稼働履歴登録')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">稼働履歴登録</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 重機一覧
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        {{ $machinery_master->machine_name }}
    </div>

    <form action="{{ route('machinery-masters.usages.create', $machinery_master) }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="font-weight-bold text-dark mb-1 small">開始日</label>
                    <input type="text" name="start_date" class="form-control datepicker" value="{{ old('start_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold text-dark mb-1 small">終了日</label>
                    <input type="text" name="end_date" class="form-control datepicker" value="{{ old('end_date', now()->format('Y-m-d')) }}">
                </div>

            </div>

            <div class="form-group mt-3">
                <label class="font-weight-bold text-dark mb-1 small">現場</label>
                <input type="text" name="work_place" class="form-control" value="{{ old('work_place') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">指図番号</label>
                <input type="text" name="instruction_number" class="form-control" value="{{ old('instruction_number') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">使用班</label>
                <input type="text" name="worker_group_in_use" class="form-control" value="{{ old('worker_group_in_use') }}">
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label class="font-weight-bold text-dark mb-1 small">開始アワー</label>
                    <input type="number" step="0.1" name="hour_meter_start" class="form-control" value="{{ old('hour_meter_start', $machinery_master->current_hour_meter) }}">
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold text-dark mb-1 small">終了アワー</label>
                    <input type="number" step="0.1" name="hour_meter_end" class="form-control" value="{{ old('hour_meter_end') }}">
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="font-weight-bold text-dark mb-1 small">備考</label>
                <textarea name="remarks" rows="3" class="form-control">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('machinery-masters.show', $machinery_master) }}" class="btn btn-sm btn-light border px-4 mr-2 font-weight-bold text-dark mb-1 shadow-sm">
                <i class="fas fa-undo mr-1"></i> 戻る </a>
            <button class="btn btn-sm btn-primary px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i> 登録 </button>
        </div>
    </form>
</div>


@endsection