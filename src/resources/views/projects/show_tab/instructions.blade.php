{{-- 作業指示書 --}}
<div class="tab-pane fade show active" id="order" role="tabpanel">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <h4 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
            <span class="badge badge-success px-3 py-2 mr-3 font-weight-normal" style="font-size: 1rem;">
                {{$project->order_type}}
            </span>
            作業指示書
        </h4>

        <div class="d-flex align-items-center mt-3 mt-md-0">
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-secondary btn-sm shadow-sm mr-2">
                <i class="fas fa-edit mr-1"></i>編集
            </a>

            <a data-fancybox data-type="iframe" href="{{ route('projects.pdf', $project) }}" class="btn btn-outline-danger btn-sm shadow-sm mr-2">
                <i class="far fa-file-pdf mr-1"></i>PDF
            </a>

            <form action="{{ route('projects.update', $project->id) }}" method="POST" class="m-0 p-0">
                @csrf @method('PATCH')
                <input type="hidden" name="order_no" value="">
                <button type="submit" class="btn btn-outline-dark btn-sm shadow-sm" onclick="return confirm('作業指示書を削除してよろしいですか？')">
                    <i class="fas fa-trash-alt mr-1"></i>削除
                </button>
            </form>
        </div>
    </div>

    @if(!empty($project->order_no))
    <div class="card shadow-sm border-0 mb-4 bg-white">

        <div class="card-header bg-white border-bottom py-3">
            <div class="row text-center text-md-left">
                <div class="col-6 col-md-3 mb-2 mb-md-0 border-right">
                    <small class="text-muted d-block font-weight-bold mb-1">指示書番号</small>
                    <span class="h5 font-weight-bold mb-0">{{ $project->order_no }}</span>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0 border-right">
                    <small class="text-muted d-block font-weight-bold mb-1">現場作業責任者</small>
                    <span class="text-dark font-weight-bold">{{ $project->site_manager ?? '--' }}</span>
                </div>
                <div class="col-6 col-md-3 border-right">
                    <small class="text-muted d-block font-weight-bold mb-1">指示日</small>
                    <span class="text-dark font-weight-bold">{{ to_wareki($project->ordered_on) ?? '' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block font-weight-bold mb-1">担当者</small>
                    <span class="text-dark font-weight-bold">{{ $project->staff_name }}</span>
                </div>
            </div>
        </div>

        <div class="card-body bg-light pt-4">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marked-alt mr-2"></i>現場情報
                            </h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted small">現場通称</dt>
                                <dd class="col-sm-8 font-weight-bold text-dark">{{ $project->site_alias }}</dd>

                                <dt class="col-sm-4 text-muted small">発注者</dt>
                                <dd class="col-sm-8">
                                    <strong class="text-dark d-block">{{ $project->owner_name }}</strong>
                                    <span class="small text-muted">{{ $project->owner_contact }}</span>
                                </dd>

                                <dt class="col-sm-4 text-muted small">作業場所</dt>
                                <dd class="col-sm-8 mb-0">
                                    <div class="text-dark">{{ $project->location_address }}</div>
                                    <div class="small text-muted">{{ $project->location_subcompartment }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                                <i class="fas fa-file-contract mr-2"></i>契約情報
                            </h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted small">
                                    @if($project->order_type ==='保育' || $project->order_type ==='林産')
                                    契約 / 補助事業名
                                    @elseif($project->order_type === '一般')
                                    契約 / 事業名
                                    @endif
                                </dt>
                                <dd class="col-sm-8 font-weight-bold text-dark">{{ $project->project_name }}</dd>

                                <dt class="col-sm-4 text-muted small">工期</dt>
                                <dd class="col-sm-8 font-weight-bold text-dark">
                                    {{ to_wareki($project->started_on) }}
                                    @if(!empty($project->completed_on))
                                    <span class="mx-2 text-muted">～</span>{{ to_wareki($project->completed_on) }}
                                    @endif
                                </dd>

                                @if($project->order_type ==='保育' || $project->order_type ==='林産')
                                @if(!empty($project->subsidy_grant_date))
                                <dt class="col-sm-4 text-muted small">補助金交付日</dt>
                                <dd class="col-sm-8 mb-0 text-dark">{{ to_wareki($project->subsidy_grant_date) }}</dd>
                                @endif
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            @if($project->order_type ==='保育' || $project->order_type ==='林産')
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body py-3">
                    <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i>履歴等
                    </h6>
                    <div class="row align-items-center">
                        <div class="col-md-4 border-right mb-3 mb-md-0">
                            <label class="small text-muted font-weight-bold mb-1">保安林の有無 / 伐採届提出日</label>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge {{ $project->is_protection_forest ? 'badge-warning' : 'badge-secondary' }} px-3 py-2 mr-2">
                                    {{ $project->is_protection_forest ? '保安林：有' : '保安林：無' }}
                                </span>
                                <span class="text-dark small font-weight-bold">{{ to_wareki($project->logging_notice_date) }}</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="small text-muted font-weight-bold mb-1">過去の施業履歴</label>
                            <div class="text-dark mb-2">{{ $project->operation_history }}</div>
                            <div class="d-flex bg-light rounded px-3 py-2 small text-muted">
                                <div class="mr-4">一次確認者: <span class="text-dark font-weight-bold ml-1">{{ $project->first_approver }}</span></div>
                                <div>二次確認者: <span class="text-dark font-weight-bold ml-1">{{ $project->second_approver }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                        <i class="fas fa-clipboard-list mr-2"></i>作業内容・詳細
                    </h6>

                    <label class="small text-muted font-weight-bold mb-2">作業内容</label>
                    <div class="p-3 bg-light rounded text-dark mb-4 border" style="white-space: pre-wrap;">{{ $project->work_content }}</div>

                    <div class="row">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <label class="small text-muted font-weight-bold mb-2">
                                {{ $project->order_type === '保育' ? '作業量' : ($project->order_type === '林産' ? '面積' : '作業量') }}
                            </label>

                            @if($project->order_type ==='保育' || $project->order_type ==='林産')
                            <div class="row no-gutters bg-light rounded p-2 text-center border">
                                <div class="col-3 border-right">
                                    <small class="text-muted d-block">{{ $project->workloaded_title1 }}</small>
                                    <span class="h5 font-weight-bold text-dark mb-0">{{ $project->area_or_volume1 }}</span>
                                </div>
                                <div class="col-3 border-right">
                                    <small class="text-muted d-block">{{ $project->workloaded_title2 }}</small>
                                    <span class="h5 font-weight-bold text-dark mb-0">{{ $project->area_or_volume2 }} </span>
                                </div>
                                <div class="col-3 border-right">
                                    <small class="text-muted d-block">{{ $project->workloaded_title3 }}</small>
                                    <span class="h5 font-weight-bold text-dark mb-0">{{ $project->area_or_volume3 }} </span>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">{{ $project->workloaded_title4 }}</small>
                                    <span class="h5 font-weight-bold text-dark mb-0">{{ $project->area_or_volume4 }} </span>
                                </div>
                            </div>
                            @elseif($project->order_type === '一般')
                            <div class="p-3 bg-light rounded border">
                                <span class="text-dark font-weight-bold h5 mb-0">{{ $project->work_volume }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-5">
                            <div class="row no-gutters bg-white border rounded p-2 text-center h-100 align-items-center">
                                @if($project->order_type ==='保育' || $project->order_type ==='林産')
                                <div class="col border-right">
                                    <small class="text-muted d-block">作業単価</small>
                                    <span class="text-dark font-weight-bold">{{ $project->unit_price }}</span>
                                </div>
                                @endif
                                @if($project->order_type === '保育')
                                <div class="col">
                                    <small class="text-muted d-block">作業人数</small>
                                    <span class="text-dark font-weight-bold">{{ $project->personnel }}</span>
                                </div>
                                @endif
                                @if($project->order_type === '一般')
                                <div class="col border-right">
                                    <small class="text-muted d-block">作業費</small>
                                    <span class="text-dark font-weight-bold">{{ $project->work_cost }}</span>
                                </div>
                                @endif
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <small class="text-muted d-block">使用機械</small>
                                    <span class="text-dark">{{ $project->machines ?: 'なし' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($project->order_type === '林産')
            <div class="card border-0 shadow-sm mb-4 border-top border-success">
                <div class="card-body">
                    <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                        <i class="fas fa-tree mr-2"></i>出材計画
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="bg-light p-3 rounded text-center h-100 border">
                                <small class="text-muted font-weight-bold d-block mb-1">出材量予定</small>
                                <div class="h4 font-weight-bold mb-2">
                                    @if(!empty($project->expected_yield1)) {{ number_format($project->expected_yield1) }} @endif
                                    @if(!empty($project->expected_yield2)) ～ {{ number_format($project->expected_yield2) }} @endif <small class="h6">㎥</small>
                                </div>
                                <hr class="my-2">
                                <small class="text-muted d-block">平均売上額(予定)</small>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="small text-muted font-weight-bold mb-1">出材品質</label>
                            <ul class="list-group small">
                                @forelse($project->ratios as $ratio)
                                <li class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted">{{ $ratio->ratio_type }}</span>
                                    <span class="font-weight-bold text-dark">{{ $ratio->ratio }} 割</span>
                                </li>
                                @empty
                                <li class="list-group-item text-muted py-2">登録なし</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted font-weight-bold mb-1">出材先</label>
                            <ul class="list-group small">
                                @forelse($project->destinations as $destination)
                                <li class="list-group-item px-3 py-2 text-truncate" title="{{ $destination->destination_type }} : {{ $destination->destination }}">
                                    <span class="badge badge-secondary mr-2">{{ $destination->destination_type }}</span> {{ $destination->destination }}
                                </li>
                                @empty
                                <li class="list-group-item text-muted py-2">登録なし</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">

                    <div class="info-box shadow-sm">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle text-danger"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text small text-dark font-weight-bold">作業留意点</span>
                            <div class="info-box-text">{!! nl2br(e($project->notes ?: '特になし')) !!}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="info-box shadow-sm">
                        <span class="info-box-icon"><i class="fa-solid fa-paperclip text-secondary"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text small text-dark font-weight-bold">添付書類</span>
                            <span class="info-box-text">{!! nl2br(e($project->attachments ?: 'なし')) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>