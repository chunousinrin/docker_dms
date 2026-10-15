@extends('layouts.adminlte')

@section('title', 'メンテナンス登録')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">整備記録登録</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm px-3">
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

    <form
        action="{{ route('machinery-masters.maintenances.create', $machinery_master) }}"
        method="POST">

        @csrf

        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">実施日</label>
                <input type="text" name="effective_date" class="form-control datepicker @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', today()->format('Y-m-d')) }}">
                @error('effective_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">メンテナンス内容</label>
                <textarea name="maintenance_details" rows="4" class="form-control @error('maintenance_details') is-invalid @enderror">{{ old('maintenance_details') }}</textarea>
                @error('maintenance_details')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">費用</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost') }}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark mb-1 small">備考</label>
                <textarea name="remarks" rows="3" class="form-control">{{ old('remarks') }}</textarea>
            </div>

            <input type="text" name="machinery_master_id" value="{{ $machinery_master->id }}" hidden>
            <input type="text" name="input_user_id" value="{{ auth()->id() }}" hidden>
            <input type="date" name="input_date" value="{{ now()->format('Y-m-d') }}" hidden>

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('machinery-masters.show', $machinery_master) }}" class="btn btn-sm btn-light border px-4 mr-2 font-weight-bold text-dark mb-1 shadow-sm"><i class="fas fa-undo mr-1"></i>戻る</a>
            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i> 登録
            </button>
        </div>
    </form>

</div>

@endsection