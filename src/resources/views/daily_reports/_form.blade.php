@php
$dailyReport = $dailyReport ?? null;
@endphp

<div class="form-group">
    <label for="work_date" class="font-weight-bold text-dark mb-1 small">勤務日</label>
    <input type="text" name="work_date" id="work_date"
        value="{{ old('work_date', $dailyReport?->work_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
        class="form-control datepicker @error('work_date') is-invalid @enderror">
    @error('work_date')
    <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header border-info">
                <h3 class="card-title">午前</h3>
            </div>
            <div class="card-body row">
                <div class="form-group col-md-8">
                    <label for="am_business_type_id" class="font-weight-bold text-dark mb-1 small">業種</label>
                    <select name=" am_business_type_id" id="am_business_type_id"
                        class="form-control @error('am_business_type_id') is-invalid @enderror">
                        <option value="">選択してください</option>
                        @foreach ($businessTypes as $businessType)
                        <option value="{{ $businessType->id }}"
                            @selected(old('am_business_type_id', $dailyReport?->am_business_type_id) == $businessType->id)>
                            {{ $businessType->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('am_business_type_id')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-4 ">
                    <label for="am_weather_id" class="font-weight-bold text-dark mb-1 small">天気</label>
                    <select name="am_weather_id" id="am_weather_id"
                        class="form-control @error('am_weather_id') is-invalid @enderror">
                        <option value="">選択してください</option>
                        @foreach ($weathers as $weather)
                        <option value="{{ $weather->id }}"
                            @selected(old('am_weather_id', $dailyReport?->am_weather_id) == $weather->id)>
                            {{ $weather->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('am_weather_id')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12 mb-0">
                    <label for="am_summary" class="font-weight-bold text-dark mb-1 small">摘要</label>
                    <textarea name="am_summary" id="am_summary" rows="4"
                        class="form-control @error('am_summary') is-invalid @enderror">{{ old('am_summary', $dailyReport?->am_summary) }}</textarea>
                    @error('am_summary')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header border-warning">
                <h3 class="card-title" class="font-weight-bold text-dark mb-1 small">午後</h3>
            </div>
            <div class="card-body row">
                <div class="form-group col-md-8">
                    <label for="pm_business_type_id" class="font-weight-bold text-dark mb-1 small">業種</label>
                    <select name="pm_business_type_id" id="pm_business_type_id"
                        class="form-control @error('pm_business_type_id') is-invalid @enderror">
                        <option value="">選択してください</option>
                        @foreach ($businessTypes as $businessType)
                        <option value="{{ $businessType->id }}"
                            @selected(old('pm_business_type_id', $dailyReport?->pm_business_type_id) == $businessType->id)>
                            {{ $businessType->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('pm_business_type_id')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="pm_weather_id" class="font-weight-bold text-dark mb-1 small">天気</label>
                    <select name="pm_weather_id" id="pm_weather_id"
                        class="form-control @error('pm_weather_id') is-invalid @enderror">
                        <option value="">選択してください</option>
                        @foreach ($weathers as $weather)
                        <option value="{{ $weather->id }}"
                            @selected(old('pm_weather_id', $dailyReport?->pm_weather_id) == $weather->id)>
                            {{ $weather->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('pm_weather_id')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12 mb-0">
                    <label for="pm_summary" class="font-weight-bold text-dark mb-1 small">摘要</label>
                    <textarea name="pm_summary" id="pm_summary" rows="4"
                        class="form-control @error('pm_summary') is-invalid @enderror">{{ old('pm_summary', $dailyReport?->pm_summary) }}</textarea>
                    @error('pm_summary')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="remarks" class="font-weight-bold text-dark mb-1 small">備考</label>
    <textarea name="remarks" id="remarks" rows="3"
        class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $dailyReport?->remarks) }}</textarea>
    @error('remarks')
    <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>