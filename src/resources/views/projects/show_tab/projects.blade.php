{{-- 事業 --}}
<div class="tab-pane fade" id="projects" role="tabpanel">

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card card-outline card-info">
                <div class="card-title p-2 border-bottom"><small>面積</small></div>
                <h5 class="card-body text-center">{{ $project->area_value }} <small>{{ $project->area_unit }}</small></h5>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-outline card-info">
                <div class="card-title p-2 border-bottom"><small>材積</small></div>
                <h5 class="card-body text-center">{{ $project->volume_value }} <small>{{ $project->volume_unit }}</small></h5>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-outline card-info">
                <div class="card-title p-2 border-bottom"><small>人工</small></div>
                <h5 class="card-body text-center">{{ number_format($project->labor_units,0) ?? 0 }}<small>人工</small></h5>
            </div>
        </div>
    </div>

    <button class="btn btn-sm btn-primary px-4 mt-2" data-toggle="modal" data-target="#editAreaModal">
        <i class="fas fa-edit"></i> 編集
    </button>
</div>
{{--事業編集モーダル--}}
<div class="modal fade" id="editAreaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('projects.updateMetrics', $project->id) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">面積・材積・人区 編集</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <style>
                        .select2-container--bootstrap4 .select2-selection {
                            /*border: none !important;*/
                            border-radius: 0 0.25rem 0.25rem 0 !important;
                            /*border-left: none !important;*/
                        }
                    </style>
                    <label>面積</label>
                    <div class="d-flex mb-3">
                        <input type="number" step="any" name="area_value" value="{{ old('area_value', $project->area_value) }}" class="form-control rounded-0 border-right-0" style="border-radius:0.25rem 0 0 0.25rem!important">
                        <select name="area_unit" class="form-control select2" style="border-left:none!important;">
                            <option value="ha" @selected(old('area_unit', $project->area_unit) === 'ha')>ha</option>
                            <option value="㎥" @selected(old('area_unit', $project->area_unit) === '㎥')>㎥</option>
                            <option value="㎡" @selected(old('area_unit', $project->area_unit) === '㎡')>㎡</option>
                            <option value="t" @selected(old('area_unit', $project->area_unit) === 't')>t</option>
                        </select>
                    </div>
                    <label>材積</label>
                    <div class="d-flex mb-3">
                        <input type="number" step="any" name="volume_value" value="{{ old('volume_value', $project->volume_value) }}" class="form-control rounded-0 border-right-0" style="border-radius:0.25rem 0 0 0.25rem!important">
                        <select name="volume_unit" class="form-control select2" style="border-left:none!important;">
                            <option value="ha" @selected(old('volume_unit', $project->volume_unit) === 'ha')>ha</option>
                            <option value="㎥" @selected(old('volume_unit', $project->volume_unit) === '㎥')>㎥</option>
                            <option value="㎡" @selected(old('volume_unit', $project->volume_unit) === '㎡')>㎡</option>
                            <option value="t" @selected(old('volume_unit', $project->volume_unit) === 't')>t</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>人工</label>
                        <input type="number" step="any" name="labor_units" value="{{ old('labor_units', $project->labor_units) }}" class="form-control rounded">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-4" data-dismiss="modal">
                        <i class="fa fa-arrow-left"></i> 戻る
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary px-4">
                        <i class="fa fa-check"></i> 更新
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>