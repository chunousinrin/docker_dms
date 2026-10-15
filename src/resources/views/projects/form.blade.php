<div class="card-body p-4 bg-light">
    <!-- タブコンテンツ -->
    <div class="tab-content" id="storeTabsContent">

        <!-- 1. 指示書共通 -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel">
            <div class="row">
                <!-- 左カラム: 基本仕様・場所 -->
                <div class="col-lg-6">
                    <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                        <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">基本情報</h5>

                        <div class="form-group mb-3">
                            <label for="order_type" class="font-weight-bold text-dark mb-1 small d-flex">
                                指示書種類 <span class="text-danger">*</span>
                            </label>
                            <select name="order_type" id="order_type" class="form-control custom-select required_field" required>
                                <option value="">選択してください</option>
                                <option value="一般" @selected(old('order_type', $project?->order_type) === '一般')>一般</option>
                                <option value="保育" @selected(old('order_type', $project?->order_type) === '保育')>保育</option>
                                <option value="林産" @selected(old('order_type', $project?->order_type) === '林産')>林産</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="business_type" class="font-weight-bold text-dark mb-1 small d-flex">
                                業務種類 <span class="text-danger">*</span>
                            </label>
                            <select id="business_type" name="business_type" class="form-control select2 custom-select required_field" required>
                                <option value="">選択または入力...</option>
                                <option value="ライフライン" @selected(old('business_type', $project?->business_type) === 'ライフライン')>ライフライン</option>
                                <option value="岐阜県" @selected(old('business_type', $project?->business_type) === '岐阜県')>岐阜県</option>
                                <option value="関市" @selected(old('business_type', $project?->business_type) === '関市')>関市</option>
                                <option value="美濃市" @selected(old('business_type', $project?->business_type) === '美濃市')>美濃市</option>
                                <option value="作業道" @selected(old('business_type', $project?->business_type) === '作業道')>作業道</option>
                                <option value="利用間伐" @selected(old('business_type', $project?->business_type) === '利用間伐')>利用間伐</option>
                                <option value="保育間伐" @selected(old('business_type', $project?->business_type) === '保育間伐')>保育間伐</option>
                                <option value="環境税間伐" @selected(old('business_type', $project?->business_type) === '環境税間伐')>環境税間伐</option>
                                <option value="脱炭素" @selected(old('business_type', $project?->business_type) === '脱炭素')>脱炭素</option>
                                <option value="花粉症" @selected(old('business_type', $project?->business_type) === '花粉症')>花粉症</option>
                                <option value="植栽" @selected(old('business_type', $project?->business_type) === '植栽')>植栽</option>
                                <option value="里山林" @selected(old('business_type', $project?->business_type) === '里山林')>里山林</option>
                                <option value="一般事業" @selected(old('business_type', $project?->business_type) === '一般事業')>一般事業</option>
                                <option value="三川公社" @selected(old('business_type', $project?->business_type) === '三川公社')>三川公社</option>
                                <option value="森林公社" @selected(old('business_type', $project?->business_type) === '森林公社')>森林公社</option>
                                <option value="公団" @selected(old('business_type', $project?->business_type) === '公団')>公団</option>
                                <option value="林道補修" @selected(old('business_type', $project?->business_type) === '林道補修')>林道補修</option>
                                <option value="木質バイオマス" @selected(old('business_type', $project?->business_type) === '木質バイオマス')>木質バイオマス</option>
                                <option value="保安林改良" @selected(old('business_type', $project?->business_type) === '保安林改良')>保安林改良</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ordered_on" class="font-weight-bold text-dark mb-1 small d-flex">
                                指示日 <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="ordered_on" id="ordered_on" value="{{ old('ordered_on', $project?->ordered_on) }}" class="form-control required_field datepicker" placeholder="指示日を選択" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="order_no" class="font-weight-bold text-dark mb-1 small d-flex">
                                指示書番号 <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <input type="text" class="form-control required_field" name="order_no" id="order_no" value="{{ old('order_no', $project?->order_no) }}" required placeholder="指示書番号を入力、または自動取得">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#orderNoModal"> <i class="fas fa-sync-alt mr-1"></i> 番号取得</button>
                                </div>
                            </div>
                            <input type="hidden" name="fiscal_year" id="fiscal_year" value="{{ old('fiscal_year', $project?->fiscal_year) }}">
                            <input type="hidden" name="group_id" id="group_id">
                        </div>
                    </div>

                    <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                        <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">作業指示・添付</h5>

                        <div class="form-group mb-3">
                            <label for="work_content" class="font-weight-bold text-dark mb-1 small">作業内容</label>
                            <textarea name="work_content" id="work_content" class="form-control" rows="2" placeholder="具体的な指示や作業内容">{{ old('work_content', $project?->work_content) }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="font-weight-bold text-dark mb-1 small">作業留意点</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="安全管理や施工時の注意点">{{ old('notes', $project?->notes) }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="attachments" class="font-weight-bold text-dark mb-1 small">添付書類</label>
                            <input type="text" name="attachments" id="attachments" value="{{ old('attachments', $project?->attachments) }}" class="form-control" placeholder="図面、計画書など">
                        </div>
                    </div>

                </div>

                <!-- 右カラム: 現場担当・工期・その他 -->
                <div class="col-lg-6">
                    <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                        <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">場所・発注者</h5>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1 small d-flex">
                                発注 / 所有者 <span class="text-danger">*</span>
                            </label>
                            <div class="form-row">
                                <div class="col-7">
                                    <input type="text" name="owner_name" value="{{ old('owner_name', $project?->owner_name) }}" class="form-control required_field" placeholder="所有者名・会社名" required>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="owner_contact" value="{{ old('owner_contact', $project?->owner_contact) }}" class="form-control" placeholder="連絡先">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1 small d-flex">
                                作業場所 <span class="text-danger">*</span>
                            </label>
                            <div class="form-row">
                                <div class="col-8">
                                    <input type="text" name="location_address" value="{{ old('location_address', $project?->location_address) }}" class="form-control required_field" placeholder="住所・所在番地" required>
                                </div>
                                <div class="col-4">
                                    <input type="text" name="location_subcompartment" value="{{ old('location_subcompartment', $project?->location_subcompartment) }}" class="form-control" placeholder="林小班">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                        <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">現場担当・工期</h5>

                        <div class="form-row mb-3">
                            <div class="col-6">
                                <label for="site_manager" class="font-weight-bold text-dark mb-1 small">現場責任者</label>
                                <input type="text" name="site_manager" id="site_manager" value="{{ old('site_manager', $project?->site_manager) }}" class="form-control" placeholder="責任者名">
                            </div>
                            <div class="col-6">
                                <label for="staff_name" class="font-weight-bold text-dark mb-1 small">担当者</label>
                                <input type="text" name="staff_name" id="staff_name" value="{{ old('staff_name', $project?->staff_name) }}" class="form-control" placeholder="担当者名">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-12">
                                <label for="site_alias" class="font-weight-bold text-dark mb-1 small d-flex">
                                    現場通称 <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="site_alias" id="site_alias" value="{{ old('site_alias', $project?->site_alias) }}" class="form-control required_field" required placeholder="現場の通称">
                                @error('site_alias') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-12">
                                <label for="project_name" class="font-weight-bold text-dark mb-1 small">事業名</label>
                                <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $project?->project_name) }}" class="form-control" placeholder="契約名・補助事業名">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1 small">工期</label>
                            <div class="input-group">
                                <input type="text" name="started_on" value="{{ old('started_on', $project?->started_on) }}" class="form-control datepicker text-center" placeholder="始期">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text border-left-0 border-right-0 text-muted">〜</span>
                                </div>
                                <input type="text" name="completed_on" value="{{ old('completed_on', $project?->completed_on) }}" class="form-control datepicker text-center" placeholder="終期">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="machines" class="font-weight-bold text-dark mb-1 small">使用機械</label>
                            <input type="text" name="machines" id="machines" value="{{ old('machines', $project?->machines) }}" class="form-control" placeholder="使用する機械・重機等">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. 種類別項目 -->
        <div class="tab-pane fade" id="work" role="tabpanel">
            <div class="card card-outline card-success shadow-sm p-3 mb-3 bg-white">

                <!-- 共通セクション -->
                <div class="">
                    <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-4" style="font-size: 1.1rem;">種類別項目</h5>

                    <div class="row forest-nursery-common">
                        <div class="col-md-6 form-group mb-3">
                            <label for="is_protection_forest" class="font-weight-bold text-dark mb-1 small">保安林の有無</label>
                            <select name="is_protection_forest" id="is_protection_forest" class="form-control custom-select">
                                <option value="">選択してください</option>
                                <option value="有" {{ old('is_protection_forest', $project?->is_protection_forest) == '有' ? 'selected' : '' }}>有</option>
                                <option value="無" {{ old('is_protection_forest', $project?->is_protection_forest) == '無' ? 'selected' : '' }}>無</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="logging_notice_date" class="font-weight-bold text-dark mb-1 small">伐採届提出日</label>
                            <input type="text" name="logging_notice_date" id="logging_notice_date" value="{{ old('logging_notice_date', $project?->logging_notice_date) }}" class="form-control datepicker" placeholder="提出年月日を選択">
                        </div>
                    </div>

                    <div class="row forest-nursery-common">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1 small">施業履歴と承認者</label>
                            <input type="text" name="operation_history" value="{{ old('operation_history', $project?->operation_history) }}" class="form-control mb-2" placeholder="施業履歴を入力">
                            <div class="form-row">
                                <div class="col-6">
                                    <input type="text" name="first_approver" value="{{ old('first_approver', $project?->first_approver) }}" class="form-control form-control-sm" placeholder="1次承認者">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="second_approver" value="{{ old('second_approver', $project?->second_approver) }}" class="form-control form-control-sm" placeholder="2次承認者">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="subsidy_grant_date" class="font-weight-bold text-dark mb-1 small">補助金交付年月日</label>
                            <input type="text" name="subsidy_grant_date" id="subsidy_grant_date" value="{{ old('subsidy_grant_date', $project?->subsidy_grant_date) }}" class="form-control datepicker" placeholder="交付年月日を選択">
                        </div>
                    </div>


                    <div class="form-group forest-nursery-common mb-3">
                        <label class="font-weight-bold text-dark mb-1 small">作業内容 / 作業量</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">作業1</span>
                            </div>
                            <input type="text" list="workloaded" class="form-control" placeholder="作業内容" name="workloaded_title1" value="{{ old('workloaded_title1', $project?->workloaded_title1) }}">
                            <input type="text" class="form-control" placeholder="面積 / 本数等の作業量" name="area_or_volume1" value="{{ old('area_or_volume1', $project?->area_or_volume1) }}">
                        </div>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">作業2</span>
                            </div>
                            <input type="text" list="workloaded" class="form-control" placeholder="作業内容" name="workloaded_title2" value="{{ old('workloaded_title2', $project?->workloaded_title2) }}">
                            <input type="text" class="form-control" placeholder="面積 / 本数等の作業量" name="area_or_volume2" value="{{ old('area_or_volume2', $project?->area_or_volume2) }}">
                        </div>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">作業3</span>
                            </div>
                            <input type="text" list="workloaded" class="form-control" placeholder="作業内容" name="workloaded_title3" value="{{ old('workloaded_title3', $project?->workloaded_title3) }}">
                            <input type="text" class="form-control" placeholder="面積 / 本数等の作業量" name="area_or_volume3" value="{{ old('area_or_volume3', $project?->area_or_volume3) }}">
                        </div>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">作業4</span>
                            </div>
                            <input type="text" list="workloaded" class="form-control" placeholder="作業内容" name="workloaded_title4" value="{{ old('workloaded_title4', $project?->workloaded_title4) }}">
                            <input type="text" class="form-control" placeholder="面積 / 本数等の作業量" name="area_or_volume4" value="{{ old('area_or_volume4', $project?->area_or_volume4) }}">
                        </div>
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
                            <input type="text" name="unit_price" id="unit_price" value="{{ old('unit_price', $project?->unit_price) }}" class="form-control" placeholder="金額または単価">
                        </div>
                    </div>
                </div>

                <!-- 各種類ごとの動的セクション -->
                <div class="row border-top pt-3">
                    <div class="col-md-6 form-group mb-3 general-only">
                        <label for="work_volume" class="text-sm font-weight-bold text-primary mb-1 small">作業量 <span class="text-xs font-weight-normal text-muted">(一般のみ)</span></label>
                        <input type="text" name="work_volume" id="work_volume" value="{{ old('work_volume', $project?->work_volume) }}" class="form-control" placeholder="作業量">
                    </div>
                    <div class="col-md-6 form-group mb-3 general-only">
                        <label for="work_cost" class="text-sm font-weight-bold text-primary mb-1 small">作業費 <span class="text-xs font-weight-normal text-muted">(一般のみ)</span></label>
                        <input type="text" name="work_cost" id="work_cost" value="{{ old('work_cost', $project?->work_cost) }}" class="form-control" placeholder="作業費">
                    </div>
                    <div class="col-md-6 form-group mb-3 nursery-only">
                        <label for="personnel" class="text-sm font-weight-bold text-success mb-1 small">作業人数 <span class="text-xs font-weight-normal text-muted">(保育のみ)</span></label>
                        <input type="text" name="personnel" id="personnel" value="{{ old('personnel', $project?->personnel) }}" class="form-control" placeholder="配置人数">
                    </div>
                    <div class="col-md-6 form-group mb-3 forest-only">
                        <label class="text-sm font-weight-bold text-info mb-1 small">出材予定量 <span class="text-xs font-weight-normal text-muted">(林産のみ)</span></label>
                        <div class="input-group">
                            <input type="text" name="expected_yield1" value="{{ old('expected_yield1', $project?->expected_yield1) }}" class="form-control text-center" placeholder="最小">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text border-left-0 border-right-0 text-muted">〜</span>
                            </div>
                            <input type="text" name="expected_yield2" value="{{ old('expected_yield2', $project?->expected_yield2) }}" class="form-control text-center" placeholder="最大">
                        </div>
                    </div>
                </div>

                <!-- 林産動的配列項目 -->
                <div class="row forest-only pt-2">
                    <div class="col-lg-6 form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="text-sm font-weight-bold text-info mb-1 small">出材品質</label>
                            <button type="button" class="btn btn-xs btn-outline-info ratio_add px-2"><i class="fa fa-plus"></i> 追加</button>
                        </div>
                        @php
                        // 1. バリデーションエラー時は old('ratios') を最優先
                        // 2. 編集画面時は $project->ratios (リレーション等) を使用
                        // 3. 新規作成時（両方空）は、最低1行分の空データを用意する
                        $currentRatios = old('ratios', isset($project) && $project->ratios->isNotEmpty() ? $project->ratios->toArray() : [[]]);
                        @endphp
                        <div class="ratio-container">
                            @foreach($currentRatios as $index => $ratio)
                            <div class="input-group ratio-wraper mb-2">
                                <!-- インデックス（$index）を動的に埋め込む -->
                                <input list="ratio_list" type="text"
                                    name="ratios[{{ $index }}][ratio_type]"
                                    value="{{ old("ratios.$index.ratio_type", data_get($ratio, 'ratio_type')) }}"
                                    class="form-control form-control-sm" placeholder="材種類">

                                <input type="number" step="0.01"
                                    name="ratios[{{ $index }}][ratio]"
                                    value="{{ old("ratios.$index.ratio", data_get($ratio, 'ratio')) }}"
                                    class="form-control form-control-sm" placeholder="割合">

                                <div class="input-group-append">
                                    <!-- 最初の1行目は消せないようにする、などの制御も可能（任意） -->
                                    <button class="btn btn-sm btn-outline-danger ratio_delete" type="button"><i class="fa fa-trash-alt"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <datalist id="ratio_list">
                            <option value="A材"></option>
                            <option value="B材"></option>
                            <option value="C材"></option>
                        </datalist>
                    </div>

                    @php
                    // 1. バリデーションエラー時は old('destinations') を最優先
                    // 2. 編集画面時は $project->destinations を使用
                    // 3. 新規作成時は、最低1行分の空データを用意する
                    $currentDestinations = old('destinations', isset($project) && $project->destinations->isNotEmpty() ? $project->destinations->toArray() : [[]]);
                    @endphp

                    <div class="col-lg-6 form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="text-sm font-weight-bold text-info mb-1 small">出材先</label>
                            <button type="button" class="btn btn-xs btn-outline-info destination_add px-2"><i class="fa fa-plus"></i> 追加</button>
                        </div>

                        <div class="destinations-container">
                            @foreach($currentDestinations as $index => $destination)
                            <div class="input-group destination-wraper mb-2">
                                <!-- 材種類 -->
                                <input list="destination_type_list" type="text"
                                    name="destinations[{{ $index }}][destination_type]"
                                    value="{{ old("destinations.$index.destination_type", data_get($destination, 'destination_type')) }}"
                                    class="form-control form-control-sm" placeholder="材種類">

                                <!-- 出材先 -->
                                <input list="destination_list" type="text"
                                    name="destinations[{{ $index }}][destination]"
                                    value="{{ old("destinations.$index.destination", data_get($destination, 'destination')) }}"
                                    class="form-control form-control-sm" placeholder="出材先">

                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-outline-danger remove-destination" type="button"><i class="fa fa-trash-alt"></i></button>
                                </div>
                            </div>
                            @endforeach
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
                        <input type="text" name="average_sales" id="average_sales" value="{{ old('average_sales', $project?->average_sales) }}" class="form-control col-md-6" placeholder="平均売上額金額">
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. 事業仔細 -->
        <div class="tab-pane fade" id="other" role="tabpanel">
            <div class="card card-outline card-info shadow-sm p-3 mb-3 bg-white mx-auto col-md-8 col-sm-12">
                <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-4" style="font-size: 1.1rem;">事業規模スペック</h5>

                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark mb-1 small">面積</label>
                    <div class="input-group">
                        <input type="text" name="area_value" value="{{ old('area_value', $project?->area_value) }}" class="form-control" placeholder="数値を入力" style="width: 70%;">
                        <input list="area_unit-options" type="text" name="area_unit" value="{{ old('area_unit', $project?->area_unit) }}" class="form-control" placeholder="単位" style="width: 30%;">
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
                        <input type="text" name="volume_value" value="{{ old('volume_value', $project?->volume_value) }}" class="form-control" placeholder="数値を入力" style="width: 70%;">
                        <input list="volume_unit-options" type="text" name="volume_unit" value="{{ old('volume_unit', $project?->volume_unit) }}" class="form-control" placeholder="単位" style="width: 30%;">
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
                    <input type="text" name="labor_units" id="labor_units" value="{{ old('labor_units', $project?->labor_units) }}" class="form-control" placeholder="想定合計人区を入力">
                </div>
            </div>
        </div>
    </div>
</div>




<!-- モーダル (中央配置・今風のシャドウ付きフラット仕様) -->
<div class="modal fade" id="orderNoModal" tabindex="-1" role="dialog" aria-labelledby="orderNoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0 p-3">
                <h5 class="modal-title font-weight-bold text-dark" id="orderNoModalLabel" style="font-size: 1.05rem;">
                    <i class="fas fa-file-invoice text-muted mr-2"></i>指示書番号の選定
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-3">
                    <label for="fiscalYearSelect" class="text-xs font-weight-bold text-secondary text-uppercase">対象年度</label>
                    <select id="fiscalYearSelect" name="fiscal_year" class="form-control custom-select">
                        @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}"
                            @selected($y==old('fiscal_year', $project?->fiscal_year ?? now()->year))>
                            {{ $y }}年度
                        </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="teamSelect" class="text-xs font-weight-bold text-secondary text-uppercase">施工班の割当</label>
                    <select id="teamSelect" class="form-control custom-select">
                    </select>
                </div>

                <div id="orderNoList" class="mb-3"></div>
                <div id="nextOrderNo" class="alert alert-info text-center font-weight-bold mb-0 shadow-none border-0" style="background-color: #e8f4fd; color: #1d6fa5;"></div>
            </div>
            <div class="modal-footer bg-light border-top-0 p-2 d-flex justify-content-end">
                <button type="button" id="closeModal" class="btn btn-primary px-4 shadow-sm" data-dismiss="modal">選択を確定</button>
            </div>
        </div>
    </div>
</div>