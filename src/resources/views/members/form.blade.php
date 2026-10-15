@extends('layouts.adminlte')

@section('title', $member->exists ? '組合員編集' : '組合員新規登録')

@section('content_header')
<h1>{{ $member->exists ? '組合員編集：' . $member->name1 : '組合員新規登録' }}</h1>
@endsection

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}">
    @csrf
    @if ($member->exists) @method('PUT') @endif

    <div class="card">
        <div class="card-header"><strong>基本情報</strong></div>
        <div class="card-body row">

            <div class="form-group col-md-3">
                <label>組合員番号 *</label>
                <input type="text" name="member_number" class="form-control" value="{{ old('member_number', $member->member_number) }}" required>
            </div>

            <div class="form-group col-md-3">
                <label>フリガナ</label>
                <input type="text" name="furigana" class="form-control" value="{{ old('furigana', $member->furigana) }}">
            </div>

            <div class="form-group col-md-3">
                <label>氏名1 *</label>
                <input type="text" name="name1" class="form-control" value="{{ old('name1', $member->name1) }}" required>
            </div>

            <div class="form-group col-md-3">
                <label>氏名2</label>
                <input type="text" name="name2" class="form-control" value="{{ old('name2', $member->name2) }}">
            </div>

            <div class="form-group col-md-3">
                <label>郵便番号</label>
                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $member->postal_code) }}">
            </div>

            <div class="form-group col-md-4">
                <label>住所1</label>
                <input type="text" name="address1" class="form-control" value="{{ old('address1', $member->address1) }}">
            </div>

            <div class="form-group col-md-5">
                <label>住所2</label>
                <input type="text" name="address2" class="form-control" value="{{ old('address2', $member->address2) }}">
            </div>

            <div class="form-group col-md-3">
                <label>職業</label>
                <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $member->occupation) }}">
            </div>

            <div class="form-group col-md-3">
                <label>性別</label>
                <select name="gender" class="form-control">
                    <option value="">未選択</option>
                    <option value="male" @selected(old('gender', $member->gender) === 'male')>男性</option>
                    <option value="female" @selected(old('gender', $member->gender) === 'female')>女性</option>
                    <option value="other" @selected(old('gender', $member->gender) === 'other')>その他</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label>生年月日</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', optional($member->birth_date)->format('Y-m-d')) }}">
            </div>

            <div class="form-group col-md-3">
                <label>電話番号</label>
                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $member->phone_number) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>組合員区分・加入情報</strong></div>
        <div class="card-body row">

            <div class="form-group col-md-3">
                <label>組合員区分</label>
                <input type="text" name="member_type" class="form-control" value="{{ old('member_type', $member->member_type) }}" placeholder="正組合員 / 准組合員">
            </div>

            <div class="form-group col-md-3">
                <label>源泉税区分</label>
                <input type="text" name="withholding_tax_type" class="form-control" value="{{ old('withholding_tax_type', $member->withholding_tax_type) }}">
            </div>

            <div class="form-group col-md-3">
                <label>加入年月日</label>
                <input type="date" name="joined_date" class="form-control" value="{{ old('joined_date', optional($member->joined_date)->format('Y-m-d')) }}">
            </div>

            <div class="form-group col-md-3">
                <label>脱退年月日</label>
                <input type="date" name="withdrawn_date" class="form-control" value="{{ old('withdrawn_date', optional($member->withdrawn_date)->format('Y-m-d')) }}">
            </div>

            <div class="form-group col-md-3">
                <label>所属地区</label>
                <select name="district_id" id="district_id" class="form-control">
                    <option value="">未選択</option>
                    @foreach ($districts as $d)
                    <option value="{{ $d->id }}" @selected(old('district_id', $member->district_id) == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    <a href="{{ route('master.districts.index') }}" target="_blank">地区マスタを編集</a>
                </small>
            </div>

            <div class="form-group col-md-3">
                <label>所属字</label>
                <select name="section_id" id="section_id" class="form-control">
                    <option value="">地区を選択してください</option>
                    @if ($member->district)
                    @foreach ($member->district->sections as $s)
                    <option value="{{ $s->id }}" @selected(old('section_id', $member->section_id) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group col-md-3">
                <label>選択</label>
                <input type="text" name="selection" class="form-control" value="{{ old('selection', $member->selection) }}">
            </div>

            <div class="form-group col-md-3">
                <label>選挙区</label>
                <input type="text" name="election_district" class="form-control" value="{{ old('election_district', $member->election_district) }}">
            </div>

            <div class="form-group col-md-2">
                <label>敬称</label>
                <input type="text" name="honorific" class="form-control" value="{{ old('honorific', $member->honorific ?? '様') }}">
            </div>

            <div class="form-group col-md-3 pt-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="newsletter_delivery" name="newsletter_delivery" value="1"
                        @checked(old('newsletter_delivery', $member->newsletter_delivery ?? true))>
                    <label class="custom-control-label" for="newsletter_delivery">広報送付する</label>
                </div>
            </div>

            <div class="form-group col-md-3 pt-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_soudai" name="is_soudai" value="1"
                        @checked(old('is_soudai', $member->is_soudai))>
                    <label class="custom-control-label" for="is_soudai">総代である</label>
                </div>
            </div>

            <div class="form-group col-md-12">
                <label>備考</label>
                <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $member->remarks) }}</textarea>
            </div>
        </div>
    </div>

    <button class="btn btn-primary">{{ $member->exists ? '更新する' : '登録する' }}</button>
    <a href="{{ $member->exists ? route('members.show', $member) : route('members.index') }}" class="btn btn-secondary">キャンセル</a>
</form>
@endsection

@push('js')
<script>
    // 地区を選択したら、その地区に属する「字」をAjaxで取得してセレクトを更新する
    document.getElementById('district_id').addEventListener('change', function() {
        const districtId = this.value;
        const sectionSelect = document.getElementById('section_id');
        sectionSelect.innerHTML = '<option value="">読み込み中...</option>';

        if (!districtId) {
            sectionSelect.innerHTML = '<option value="">地区を選択してください</option>';
            return;
        }

        fetch(`/api/districts/${districtId}/sections`)
            .then(res => res.json())
            .then(sections => {
                sectionSelect.innerHTML = '<option value="">未選択</option>';
                sections.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    sectionSelect.appendChild(opt);
                });
            });
    });
</script>
@endpush