@extends('layouts.adminlte')

@section('title', '給油記録登録')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">給油記録登録</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 重機一覧
        </a>
    </div>
</div>

@stop

@section('content')
<div class="card">

    <div class="card-header">
        {{ $machinery_master->machine_name }}
    </div>

    <form
        action="{{ route('machinery-masters.refuels.create', $machinery_master) }}"
        method="POST">

        @csrf

        <div class="card-body">

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">給油日</label>
                <input type="text" name="refuel_date" class="form-control datepicker" value="{{ old('refuel_date', now()->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">給油量(L)</label>
                <input type="number" step="0.01" name="fuel_volume" class="form-control" value="{{ old('fuel_volume') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">給油所</label>
                <input type="text" name="gas_station" class="form-control" value="{{ old('gas_station') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">給油場所</label>
                <input type="text" name="fuel_location" class="form-control" value="{{ old('fuel_location') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">備考</label>
                <textarea name="remarks" rows="3" class="form-control">{{ old('remarks') }}</textarea>
            </div>

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('machinery-masters.show', $machinery_master) }}" class="btn btn-sm btn-light border px-4 mr-2 font-weight-bold text-dark mb-1 shadow-sm">
                <i class="fas fa-undo mr-1"></i> 戻る </a>
            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i> 登録
            </button>
        </div>

    </form>
</div>

@endsection