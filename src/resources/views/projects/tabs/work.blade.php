<div class="card card-outline card-success shadow-sm p-3 mb-3 bg-white">
    <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-4" style="font-size: 1.1rem;">種類別項目</h5>

    <div class="row forest-nursery-common">
        <div class="col-md-6 form-group mb-3">
            <label for="is_protection_forest" class="font-weight-bold text-dark mb-1 small">保安林の有無</label>
            <select name="is_protection_forest" id="is_protection_forest" class="form-control custom-select">
                <option value="">選択してください</option>
                <option value="有" {{ old('is_protection_forest') == '有' ? 'selected' : '' }}>有</option>
                <option value="無" {{ old('is_protection_forest') == '無' ? 'selected' : '' }}>無</option>
            </select>
        </div>
        <div class="col-md-6 form-group mb-3">
            <label for="logging_notice_date" class="font-weight-bold text-dark mb-1 small">伐採届提出日</label>
            <input type="text" name="logging_notice_date" id="logging_notice_date" value="{{ old('logging_notice_date') }}" class="form-control datepicker" placeholder="提出年月日を選択">
        </div>
    </div>

    <div class="row forest-nursery-common">
        <div class="col-md-6 form-group mb-3">
            <label class="font-weight-bold text-dark mb-1 small">施業履歴と承認者</label>
            <input type="text" name="operation_history" value="{{ old('operation_history') }}" class="form-control mb-2" placeholder="施業履歴を入力">
            <div class="form-row">
                <div class="col-6">
                    <input type="text" name="first_approver" value="{{ old('first_approver') }}" class="form-control form-control-sm" placeholder="1次承認者">
                </div>
                <div class="col-6">
                    <input type="text" name="second_approver" value="{{ old('second_approver') }}" class="form-control form-control-sm" placeholder="2次承認者">
                </div>
            </div>
        </div>
        <div class="col-md-6 form-group mb-3">
            <label for="subsidy_grant_date" class="font-weight-bold text-dark mb-1 small">補助金交付年月日</label>
            <input type="text" name="subsidy_grant_date" id="subsidy_grant_date" value="{{ old('subsidy_grant_date') }}" class="form-control datepicker" placeholder="交付年月日を選択">
        </div>
    </div>

    <div class="form-group forest-nursery-common mb-3">
        <label class="font-weight-bold text-dark mb-1 small">作業内容 / 作業量</label>
        @for ($i = 1; $i <= 4; $i++)
            <div class="input-group mb-1">
            <div class="input-group-prepend"> <span class="input-group-text text-xs">作業{{ $i }}</span> </div>
            <input type="text" list="workloaded" class="form-control" placeholder="作業内容" name="workloaded_title{{ $i }}" value="{{ old('workloaded_title'.$i) }}">
            <input type="text" class="form-control" placeholder="面積 / 本数等の作業量" name="area_or_volume{{ $i }}" value="{{ old('area_or_volume'.$i) }}">
    </div>
    @endfor
    <datalist id="workloaded">
        <option value="総面積">総面積</option>
        <option value="切捨">切捨</option>
        <option value="枝打ち">枝打ち</option>
        <option value="下刈り">下刈り</option>
        <option value="除伐">除伐</option>
        <option value="測量">測量</option>
        <option value="搬出">搬出</option>
    </datalist>
</div>

<div class="row forest-nursery-common">
    <div class="col-md-6 form-group mb-3">
        <label for="unit_price" class="font-weight-bold text-dark mb-1 small">作業単価</label>
        <input type="text" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" class="form-control" placeholder="金額または単価">
    </div>
</div>

<div class="row border-top pt-3">
    <div class="col-md-6 form-group mb-3 general-only">
        <label for="work_volume" class="text-sm font-weight-bold text-primary mb-1 small">作業量 <span class="text-xs font-weight-normal text-muted">(一般のみ)</span></label>
        <input type="text" name="work_volume" id="work_volume" value="{{ old('work_volume') }}" class="form-control" placeholder="作業量">
    </div>
    <div class="col-md-6 form-group mb-3 general-only">
        <label for="work_cost" class="text-sm font-weight-bold text-primary mb-1 small">作業費 <span class="text-xs font-weight-normal text-muted">(一般のみ)</span></label>
        <input type="text" name="work_cost" id="work_cost" value="{{ old('work_cost') }}" class="form-control" placeholder="作業費">
    </div>
    <div class="col-md-6 form-group mb-3 nursery-only">
        <label for="personnel" class="text-sm font-weight-bold text-success mb-1 small">作業人数 <span class="text-xs font-weight-normal text-muted">(保育のみ)</span></label>
        <input type="text" name="personnel" id="personnel" value="{{ old('personnel') }}" class="form-control" placeholder="配置人数">
    </div>
    <div class="col-md-6 form-group mb-3 forest-only">
        <label class="text-sm font-weight-bold text-info mb-1 small">出材予定量 <span class="text-xs font-weight-normal text-muted">(林産のみ)</span></label>
        <div class="input-group">
            <input type="text" name="expected_yield1" value="{{ old('expected_yield1') }}" class="form-control text-center" placeholder="最小">
            <div class="input-group-prepend input-group-append">
                <span class="input-group-text border-left-0 border-right-0 text-muted">〜</span>
            </div>
            <input type="text" name="expected_yield2" value="{{ old('expected_yield2') }}" class="form-control text-center" placeholder="最大">
        </div>
    </div>
</div>

<div class="row forest-only pt-2">
    <div class="col-lg-6 form-group mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="text-sm font-weight-bold text-info mb-1 small">出材品質</label>
            <button type="button" class="btn btn-xs btn-outline-info ratio_add px-2"><i class="fa fa-plus"></i> 追加</button>
        </div>
        <div class="ratio-container">
            <div class="input-group ratio-wraper mb-2">
                <input list="ratio_list" type="text" name="ratios[0][ratio_type]" class="form-control form-control-sm" placeholder="材種類">
                <input type="number" step="0.01" name="ratios[0][ratio]" class="form-control form-control-sm" placeholder="割合">
                <div class="input-group-append">
                    <button class="btn btn-sm btn-outline-danger remove-ratio" type="button"><i class="fa fa-trash-alt"></i></button>
                </div>
            </div>
        </div>
        <datalist id="ratio_list">
            <option value="A材"></option>
            <option value="B材"></option>
            <option value="C材"></option>
        </datalist>
    </div>

    <div class="col-lg-6 form-group mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="text-sm font-weight-bold text-info mb-1 small">出材先</label>
            <button type="button" class="btn btn-xs btn-outline-info destination_add px-2"><i class="fa fa-plus"></i> 追加</button>
        </div>
        <div class="destinations-container">
            <div class="input-group destination-wraper mb-2">
                <input list="destination_type_list" type="text" name="destinations[0][destination_type]" class="form-control form-control-sm" placeholder="材種類">
                <input list="destination_list" type="text" name="destinations[0][destination]" class="form-control form-control-sm" placeholder="出材先">
                <div class="input-group-append">
                    <button class="btn btn-sm btn-outline-danger remove-destination" type="button"><i class="fa fa-trash-alt"></i></button>
                </div>
            </div>
        </div>
        <datalist id="destination_type_list">
            <option value="A材"></option>
            <option value="B材"></option>
            <option value="C材"></option>
            <option value="その他"></option>
        </datalist>
        <datalist id="destination_list">
            <option value="長良川木協"></option>
            <option value="森の合板"></option>
            <option value="自然応用化学"></option>
            <option value="県森連(共販所)"></option>
        </datalist>
    </div>

    <div class="col-12 form-group mb-3">
        <label for="average_sales" class="text-sm font-weight-bold text-info mb-1 small">平均売上額</label>
        <input type="text" name="average_sales" id="average_sales" value="{{ old('average_sales') }}" class="form-control col-md-6" placeholder="平均売上額金額">
    </div>
</div>