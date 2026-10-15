                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                                <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">基本情報</h5>

                                <div class="form-group mb-3">
                                    <label for="order_type" class="font-weight-bold text-dark mb-1 small d-flex">
                                        指示書種類 <span class="text-danger">*</span>
                                    </label>
                                    <select name="order_type" id="order_type" class="form-control custom-select required_field" required>
                                        <option value="">選択してください</option>
                                        <option value="一般" {{ old('order_type') == '一般' ? 'selected' : '' }}>一般</option>
                                        <option value="保育" {{ old('order_type') == '保育' ? 'selected' : '' }}>保育</option>
                                        <option value="林産" {{ old('order_type') == '林産' ? 'selected' : '' }}>林産</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="business_type" class="font-weight-bold text-dark mb-1 small d-flex">
                                        業務種類 <span class="text-danger">*</span>
                                    </label>
                                    <select id="business_type" name="business_type" class="form-control custom-select required_field js-choices" required>
                                        <option value="">選択または入力...</option>
                                        @foreach(['ライフライン', '岐阜県', '関市', '美濃市', '作業道', '利用間伐', '保育間伐', '環境税間伐', '脱炭素', '花粉症', '植栽', '里山林', '一般事業', '三川公社', '森林公社', '公団', '林道補修', '木質バイオマス', '保安林改良'] as $type)
                                        <option value="{{ $type }}" {{ old('business_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="ordered_on" class="font-weight-bold text-dark mb-1 small d-flex">
                                        指示日 <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="ordered_on" id="ordered_on" value="{{ old('ordered_on') }}" class="form-control required_field datepicker" placeholder="指示日を選択" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="order_no" class="font-weight-bold text-dark mb-1 small d-flex">
                                        指示書番号 <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control required_field" name="order_no" id="order_no" value="{{ old('order_no') }}" required placeholder="指示書番号を入力、または自動取得">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary btn-sm" type="button" data-toggle="modal" data-target="#orderNoModal">
                                                <i class="fas fa-sync-alt mr-1"></i> 番号取得
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="fiscal_year" id="fiscal_year" value="{{ old('fiscal_year') }}">
                                    <input type="hidden" name="group_id" id="group_id" value="{{ old('group_id') }}">
                                </div>
                            </div>

                            <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                                <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">作業指示・添付</h5>

                                <div class="form-group mb-3">
                                    <label for="work_content" class="font-weight-bold text-dark mb-1 small">作業内容</label>
                                    <textarea name="work_content" id="work_content" class="form-control" rows="2" placeholder="具体的な指示や作業内容">{{ old('work_content') }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes" class="font-weight-bold text-dark mb-1 small">作業留意点</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="安全管理や施工時の注意点">{{ old('notes') }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="attachments" class="font-weight-bold text-dark mb-1 small">添付書類</label>
                                    <input type="text" name="attachments" id="attachments" value="{{ old('attachments') }}" class="form-control" placeholder="図面、計画書など">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                                <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">場所・発注者</h5>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1 small d-flex">
                                        発注 / 所有者 <span class="text-danger">*</span>
                                    </label>
                                    <div class="form-row">
                                        <div class="col-7">
                                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="form-control required_field" placeholder="所有者名・会社名" required>
                                        </div>
                                        <div class="col-5">
                                            <input type="text" name="owner_contact" value="{{ old('owner_contact') }}" class="form-control" placeholder="連絡先">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1 small d-flex">
                                        作業場所 <span class="text-danger">*</span>
                                    </label>
                                    <div class="form-row">
                                        <div class="col-8">
                                            <input type="text" name="location_address" value="{{ old('location_address') }}" class="form-control required_field" placeholder="住所・所在番地" required>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" name="location_subcompartment" value="{{ old('location_subcompartment') }}" class="form-control" placeholder="林小班">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-primary shadow-sm p-3 mb-4 bg-white">
                                <h5 class="text-dark font-weight-bold border-bottom pb-2 mb-3" style="font-size: 1rem;">現場担当・工期</h5>

                                <div class="form-row mb-3">
                                    <div class="col-6">
                                        <label for="site_manager" class="font-weight-bold text-dark mb-1 small">現場責任者</label>
                                        <input type="text" name="site_manager" id="site_manager" value="{{ old('site_manager') }}" class="form-control" placeholder="責任者名">
                                    </div>
                                    <div class="col-6">
                                        <label for="staff_name" class="font-weight-bold text-dark mb-1 small">担当者</label>
                                        <input type="text" name="staff_name" id="staff_name" value="{{ old('staff_name', Auth::user()->name ?? '') }}" class="form-control" placeholder="担当者名">
                                    </div>
                                </div>

                                <div class="form-row mb-3">
                                    <div class="col-12">
                                        <label for="site_alias" class="font-weight-bold text-dark mb-1 small d-flex">
                                            現場通称 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="site_alias" id="site_alias" value="{{ old('site_alias') }}" class="form-control required_field" required placeholder="現場の通称">
                                        @error('site_alias') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-row mb-3">
                                    <div class="col-12">
                                        <label for="project_name" class="font-weight-bold text-dark mb-1 small">事業名</label>
                                        <input type="text" name="project_name" id="project_name" value="{{ old('project_name') }}" class="form-control" placeholder="契約名・補助事業名">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1 small">工期</label>
                                    <div class="input-group">
                                        <input type="text" name="started_on" value="{{ old('started_on') }}" class="form-control datepicker text-center" placeholder="始期">
                                        <div class="input-group-prepend input-group-append">
                                            <span class="input-group-text border-left-0 border-right-0 text-muted">〜</span>
                                        </div>
                                        <input type="text" name="completed_on" value="{{ old('completed_on') }}" class="form-control datepicker text-center" placeholder="終期">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="machines" class="font-weight-bold text-dark mb-1 small">使用機械</label>
                                    <input type="text" name="machines" id="machines" value="{{ old('machines') }}" class="form-control" placeholder="使用する機械・重機等">
                                </div>
                            </div>
                        </div>
                    </div>