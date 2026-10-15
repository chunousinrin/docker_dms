@php
$selectedAttachments = old(
'attachment_ids',
isset($machinery)
? $machinery->attachments->pluck('id')->toArray()
: []
);
@endphp
{{-- 1. 基本情報ブロック --}}
<h6 class="font-weight-bold text-dark mb-3">
    <i class="fas fa-info-circle mr-1 text-secondary"></i> 基本情報
</h6>
<div class="row mb-4">
    {{-- 重機名 --}}
    <div class="col-md-5">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">重機名 <span class="text-danger">*</span></label>
            <input type="text" name="machine_name" class="form-control @error('machine_name') is-invalid @enderror required_field" value="{{ old('machine_name', $machinery->machine_name) }}" required>
            @error('machine_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- メーカー --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">メーカー</label>
            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $machinery->manufacturer) }}">
        </div>
    </div>

    {{-- 分類 --}}
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">分類</label>
            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror required_field" required>
                <option value="">選択してください</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $machinery->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->category_name }}
                </option>
                @endforeach
            </select>
            @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- 2. 状態・管理ブロック --}}
<h6 class="font-weight-bold text-dark mb-3 border-top pt-4">
    <i class=" fas fa-tasks mr-1 text-secondary"></i> 状態・管理情報
</h6>
<div class="row mb-4">
    {{-- 管理番号 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">管理番号</label>
            <input type="text" name="identification_number" class="form-control @error('identification_number') is-invalid @enderror" value="{{ old('identification_number', $machinery->identification_number) }}">
            @error('identification_number')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- 状態 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">状態</label>
            <select name="status" class="form-control">
                <option value="active" {{ old('status', $machinery->status) == 'active' ? 'selected' : '' }}>稼働中</option>
                <option value="maintenance" {{ old('status', $machinery->status) == 'maintenance' ? 'selected' : '' }}>整備中</option>
                <option value="repair" {{ old('status', $machinery->status) == 'repair' ? 'selected' : '' }}>修理中</option>
                <option value="retired" {{ old('status', $machinery->status) == 'retired' ? 'selected' : '' }}>廃車</option>
                <option value="returned" {{ old('status', $machinery->status) == 'returned' ? 'selected' : '' }}>返却</option>
            </select>
        </div>
    </div>

    {{-- 所有区分 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">所有区分</label>
            <select name="ownership" id="ownership_select" class="form-control">
                <option value="owned" {{ old('ownership', $machinery->ownership) == 'owned' ? 'selected' : '' }}>自社</option>
                <option value="lease" {{ old('ownership', $machinery->ownership) == 'lease' ? 'selected' : '' }}>リース</option>
                <option value="rental" {{ old('ownership', $machinery->ownership) == 'rental' ? 'selected' : '' }}>レンタル</option>
            </select>
        </div>
    </div>

    {{-- 管理者 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">管理者</label>
            <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $machinery->manager_name) }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">ベースマシン</label>
            <input type="text" name="base_machine" class="form-control" value="{{ old('base_machine', $machinery->base_machine) }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">規格</label>
            <input type="text" name="standard" class="form-control" value="{{ old('standard', $machinery->standard) }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">整備工場</label>
            <input type="text" name="service_factory" class="form-control" value="{{ old('service_factory', $machinery->service_factory) }}">
        </div>
    </div>

</div>

{{-- 3. 日程情報・メーターブロック --}}
<h6 class="font-weight-bold text-dark mb-3 border-top pt-4">
    <i class="fas fa-calendar-alt mr-1 text-secondary"></i> 日程・稼働情報
</h6>
<div class="row mb-4">
    {{-- アワーメーター --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">現在アワーメーター</label>
            <input type="number" step="0.1" name="current_hour_meter" class="form-control" value="{{ old('current_hour_meter', $machinery->current_hour_meter) }}">
        </div>
    </div>

    {{-- 導入日 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">導入日</label>
            <input type="text" name="introduction_date" class="form-control datepicker" value="{{ old('introduction_date', $machinery->introduction_date) }}">
        </div>
    </div>

    {{-- 返却日（自社の場合はJavaScriptで非表示化） --}}
    <div class="col-md-3" id="return_date_wrapper">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">返却日</label>
            <input type="text" name="return_date" class="form-control datepicker" value="{{ old('return_date', $machinery->return_date) }}">
        </div>
    </div>

    {{-- 次回点検日 --}}
    <div class="col-md-3">
        <div class="form-group">
            <label class="font-weight-bold text-dark mb-1 small">次回点検日</label>
            <input type="text" name="next_maintenance_date" class="form-control datepicker" value="{{ old('next_maintenance_date', $machinery->next_maintenance_date) }}">
        </div>
    </div>
</div>

{{-- 4. 装備情報 --}}
<div class="form-group mb-4 border-top pt-4">
    <label class="font-weight-bold text-dark mb-3 d-block"><i class="fas fa-paperclip mr-1 text-secondary"></i> 装備</label>
    <div class="row">
        @foreach($attachments as $attachment)
        <div class="col-md-3 mb-3">
            <div class="form-check bg-light p-2 rounded shadow-sm d-flex align-items-center border-left border-info" style="padding-left: 2.5rem !important; border-left-width: 6px !important;">
                {{-- 修正箇所：$selectedAttachments が無い場合は 空の配列 [] をデフォルトにするよう変更 --}}
                <input type="checkbox"
                    name="attachment_ids[]"
                    value="{{ $attachment->id }}"
                    id="attachment_{{ $attachment->id }}"

                    {{ in_array($attachment->id, $selectedAttachments) ? 'checked' : '' }}>
                <label class="form-check-label text-dark w-100 py-1 ml-2" for="attachment_{{ $attachment->id }}" style="cursor: pointer;">
                    {{ $attachment->model_name }}
                </label>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- 5. 備考 --}}
<div class="form-group mb-5 border-top pt-4">
    <label class="font-weight-bold text-dark">
        <i class="far fa-clipboard text-secondary mr-1"></i> 備考</label>
    <textarea name="remarks" rows="4" class="form-control">{{ old('remarks', $machinery->remarks) }}</textarea>
</div>

{{-- ボタンエリア --}}
<div class="text-right border-top pt-4">
    <a href="{{ route('machinery-masters.index') }}" class="btn btn-sm btn-light border px-4 mr-2 font-weight-bold text-dark mb-1"><i class="fas fa-undo mr-1"></i>戻る</a>
    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm font-weight-bold"><i class="fas fa-save mr-1"></i>登録する</button>
</div>

{{-- 所有区分連動用のJavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ownershipSelect = document.getElementById('ownership_select');
        const returnDateWrapper = document.getElementById('return_date_wrapper');

        function toggleReturnDate() {
            if (ownershipSelect.value === 'owned') {
                returnDateWrapper.style.display = 'none';
            } else {
                returnDateWrapper.style.display = 'block';
            }
        }

        // 初期表示時の判定
        toggleReturnDate();

        // 変更時のイベントリスナー
        ownershipSelect.addEventListener('change', toggleReturnDate);
    });
</script>