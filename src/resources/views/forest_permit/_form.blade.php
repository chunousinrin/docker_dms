<div class="row">
    {{-- 左カラム：基本情報・顧客情報 --}}
    <div class="col-md-8">
        {{-- mb-4 を追加してカード間の余白を確保 --}}
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h5 class="text-dark font-weight-bold mb-0" style="font-size: 1rem;">基本・物件情報</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">整理番号 - 枝番 <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="management_no" class="required_field form-control @error('management_no') is-invalid @enderror" placeholder="管理番号" value="{{ old('management_no', $application->management_no ?? '') }}" required>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">-</span>
                            </div>
                            <input type="number" name="branch_no" class="form-control @error('branch_no') is-invalid @enderror" placeholder="枝番" value="{{ old('branch_no', $application->branch_no ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">施設名称</label>
                        <input type="text" name="facility_name" class="form-control" placeholder="施設名や事業名を入力" value="{{ old('facility_name', $application->facility_name ?? '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">保安林種</label>
                        <input type="text" name="protection_forest_type" class="form-control" placeholder="例: 保健保安林" value="{{ old('protection_forest_type', $application->protection_forest_type ?? '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">森林所在地</label>
                        <input type="text" name="forest_location" class="form-control" placeholder="市町村・番地を入力" value="{{ old('forest_location', $application->forest_location ?? '') }}">
                    </div>

                    {{-- form-row を通常の row に統一（Bootstrapのバージョンに合わせる） --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-dark mb-1 small">筆数</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="parcel_count" class="form-control" value="{{ old('parcel_count', $application->parcel_count ?? '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">筆</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-dark mb-1 small">許可面積</label>
                                <div class="input-group">
                                    <input type="number" step="0.00001" name="permit_area" class="form-control" placeholder="0.00000" value="{{ old('permit_area', $application->permit_area ?? '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">ha</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h5 class="text-dark font-weight-bold mb-0" style="font-size: 1rem;">申請者情報・備考</h5>
            </div>
            <div class="card-body">
                {{-- rowで包むことで崩れを防止 --}}
                <div class="row">
                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">申請者氏名 / 法人名</label>
                        <input type="text" name="applicant_name" class="form-control" value="{{ old('applicant_name', $application->applicant_name ?? '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">連絡先（電話番号・メール等）</label>
                        <input type="text" name="contact" class="form-control" placeholder="例: 090-0000-0000" value="{{ old('contact', $application->contact ?? '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">備考</label>
                        <textarea name="remarks" rows="4" class="form-control" placeholder="特記事項や変更履歴などがあれば入力してください">{{ old('remarks', $application->remarks ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 右カラム：許可情報・進捗 --}}
    <div class="col-md-4">
        <div class="card card-outline card-info mb-4">
            <div class="card-header">
                <h5 class="text-dark font-weight-bold mb-0" style="font-size: 1rem;">許可情報</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">申請年月日 <span class="text-danger">*</span></label>
                        <input type="text" name="application_date" class="form-control datepicker required_field" value="{{ old('application_date', isset($application) && $application->application_date ? $application->application_date->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">許可年月日 <span class="text-danger">*</span></label>
                        <input type="text" name="permit_date" class="form-control datepicker required_field" value="{{ old('permit_date', isset($application) && $application->permit_date ? $application->permit_date->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">指令番号</label>
                        <input type="text" name="order_no" class="form-control" value="{{ old('order_no', $application->order_no ?? '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">許可始期 <span class="text-danger">*</span></label>
                        <input type="text" name="permit_start_date" class="form-control datepicker required_field" value="{{ old('permit_start_date', isset($application) && $application->permit_start_date ? $application->permit_start_date->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">許可終期 <span class="text-danger">*</span></label>
                        <input type="text" name="permit_end_date" class="form-control datepicker required_field" value="{{ old('permit_end_date', isset($application) && $application->permit_end_date ? $application->permit_end_date->format('Y-m-d') : '') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-info mb-4">
            <div class="card-header">
                <h5 class="text-dark font-weight-bold mb-0" style="font-size: 1rem;">進捗管理</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">提出日</label>
                        <input type="text" name="submission_date" class="form-control datepicker" value="{{ old('submission_date', isset($application) && $application->submission_date ? $application->submission_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">着手日</label>
                        <input type="text" name="start_date" class="form-control datepicker" value="{{ old('start_date', isset($application) && $application->start_date ? $application->start_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">完了日</label>
                        <input type="text" name="completion_date" class="form-control datepicker" value="{{ old('completion_date', isset($application) && $application->completion_date ? $application->completion_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">皆伐完了日</label>
                        <input type="text" name="clear_cut_completion_date" class="form-control datepicker" value="{{ old('clear_cut_completion_date', isset($application) && $application->clear_cut_completion_date ? $application->clear_cut_completion_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold text-dark mb-1 small">植栽完了日</label>
                        <input type="text" name="planting_completion_date" class="form-control datepicker" value="{{ old('planting_completion_date', isset($application) && $application->planting_completion_date ? $application->planting_completion_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>