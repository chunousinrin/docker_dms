<div class="card card-outline card-info shadow-sm p-3 mb-3 bg-white mx-auto col-md-8 col-sm-12">
    <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-4" style="font-size: 1.1rem;">事業規模スペック</h5>

    <div class="form-group mb-3">
        <label class="font-weight-bold text-dark mb-1 small">面積</label>
        <div class="input-group">
            <input type="text" name="area_value" value="{{ old('area_value') }}" class="form-control w-75" placeholder="数値を入力">
            <input list="area_unit-options" type="text" name="area_unit" value="{{ old('area_unit') }}" class="form-control w-25" placeholder="単位">
            <datalist id="area_unit-options">
                <option value="ha"></option>
                <option value="㎥"></option>
                <option value="㎡"></option>
                <option value="t"></option>
            </datalist>
        </div>
    </div>

    <div class="form-group mb-3">
        <label class="font-weight-bold text-dark mb-1 small">材積</label>
        <div class="input-group">
            <input type="text" name="volume_value" value="{{ old('volume_value') }}" class="form-control w-75" placeholder="数値を入力">
            <input list="volume_unit-options" type="text" name="volume_unit" value="{{ old('volume_unit') }}" class="form-control w-25" placeholder="単位">
            <datalist id="volume_unit-options">
                <option value="ha"></option>
                <option value="㎥"></option>
                <option value="㎡"></option>
                <option value="t"></option>
            </datalist>
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="labor_units" class="font-weight-bold text-dark mb-1 small">人区</label>
        <input type="text" name="labor_units" id="labor_units" value="{{ old('labor_units') }}" class="form-control" placeholder="想定合計人区を入力">
    </div>
</div>