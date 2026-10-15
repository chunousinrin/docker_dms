@extends('layouts.adminlte')

@section('title', '重機詳細')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">{{ $machinery->machine_name }}</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 重機一覧
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card search-card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs nav-justified" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">基本情報</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">整備履歴</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab4-tab" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false">給油記録</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab5-tab" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false">稼働履歴</a>
                </li>
            </ul>

        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <div class="card-text">
                        <div class="row">
                            <div class="col-lg-7">

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light font-weight-bold">
                                        <i class="fas fa-info-circle mr-2 text-primary"></i>基本情報
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4 text-muted mb-1">機械名</div>
                                            <div class="col-sm-8 mb-3 font-weight-bold text-dark">{{ $machinery->machine_name }}</div>

                                            <div class="col-sm-4 text-muted mb-1">識別番号 (管理No.)</div>
                                            <div class="col-sm-8 mb-3">
                                                <span class="badge badge-secondary">{{ $machinery->identification_number ?? '---' }}</span>
                                            </div>

                                            <div class="col-sm-4 text-muted mb-1">メーカー</div>
                                            <div class="col-sm-8 mb-3">{{ $machinery->manufacturer }}</div>

                                            <div class="col-sm-4 text-muted mb-1">ベースマシン / 仕様・規格</div>
                                            <div class="col-sm-8 mb-3">
                                                {{ $machinery->base_machine }}
                                                @if($machinery->standard)
                                                <span class="text-muted">（{{ $machinery->standard }}）</span>
                                                @endif
                                            </div>

                                            <div class="col-sm-4 text-muted mb-1">カテゴリ</div>
                                            <div class="col-sm-8 mb-0">
                                                {{ $machinery->category?->category_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light font-weight-bold">
                                        <i class="fas fa-tools mr-2 text-success"></i>稼働・メンテナンス情報
                                    </div>
                                    <div class="card-body">
                                        <div class="row border-bottom pb-2 mb-3">
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">現在の稼働時間 (アワーメーター)</small>
                                                <span class="h4 font-weight-bold text-success">{{ number_format($machinery->current_hour_meter) ?? '---' }}</span> <small>h</small>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">次回点検予定日</small>
                                                @if(!is_null($machinery->next_maintenance_date))
                                                <span class="h5 font-weight-bold text-danger">
                                                    {{ $machinery->next_maintenance_date->format('Y/m/d') }}</span>
                                                @else
                                                ---
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4 text-muted mb-1">ステータス</div>
                                            <div class="col-sm-8 mb-3">
                                                @php
                                                $statusClass = match($machinery->status) {
                                                'active' => 'success',
                                                'maintenance' => 'warning',
                                                'repair' => 'danger',
                                                'retired' => 'secondary',
                                                default => 'secondary',
                                                };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ $machinery->status_label }}
                                                </span>
                                            </div>

                                            <div class="col-sm-4 text-muted mb-1">所有区分</div>
                                            <div class="col-sm-8 mb-3">{{ $machinery->ownership_label }}</div>

                                            <div class="col-sm-4 text-muted mb-1">導入日 / 返却日</div>
                                            <div class="col-sm-8 mb-3">
                                                {{ $machinery->introduction_date->format('Y/m/d') ?? '---' }}
                                                @if(!is_null($machinery->return_date))
                                                〜 {{ $machinery->return_date->format('Y/m/d') ?? '---' }}
                                                @endif
                                            </div>

                                            <div class="col-sm-4 text-muted mb-1">管理責任者</div>
                                            <div class="col-sm-8 mb-3">{{ $machinery->manager_name ?? '---' }}</div>

                                            <div class="col-sm-4 text-muted mb-1">対応サービス工場</div>
                                            <div class="col-sm-8 mb-0">{{ $machinery->service_factory }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light font-weight-bold">装備</div>
                                    <div class="card-body">
                                        @forelse($machinery->attachments as $attachment)
                                        <div class="py-2 d-flex justify-content-start align-items-center"><span class="badge mr-1" style="background:#D4EBBF">{{ $loop->iteration }}.</span> {{ $attachment->model_name }} </div>
                                        @empty
                                        <div class=" text-muted">
                                            装備なし
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light font-weight-bold">備考</div>
                                    <div class="card-body">
                                        <p class="card-text text-secondary style-preserve-newlines">{{ $machinery->remarks ?? '特記事項なし' }}</p>
                                    </div>
                                </div>

                                <div class="card shadow-sm bg-light text-muted">
                                    <div class="card-body p-3">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2">システム管理情報</h6>
                                        <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                            <li class="mb-1"><strong>システムID:</strong> {{ $machinery->id }}</li>
                                            <li class="mb-1"><strong>登録日時:</strong> {{ $machinery->created_at }}</li>
                                            <li class="mb-1"><strong>最終更新:</strong> {{ $machinery->updated_at }}</li>
                                            @if($machinery->deleted_at)
                                            <li class="text-danger"><strong>削除日時:</strong> {{ $machinery->deleted_at }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <a href="{{ route('machinery-masters.edit', $machinery) }}" class="btn btn-sm btn-block shadow-sm btn-warning px-3">
                                    <i class="fas fa-edit mr-1"></i> 編集
                                </a>
                                <a href="{{ route('machinery-masters.index') }}" class="btn btn-sm btn-block shadow-sm btn-light px-3">
                                    <i class="fas fa-undo-alt mr-1"></i> 一覧へ戻る
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    <div class="card-title w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>整備履歴</h5>
                            <a href="{{ route('machinery-masters.maintenances.create', $machinery) }}"
                                class="btn btn-primary btn-sm shadow-sm mb-3">
                                <i class="fas fa-plus mr-1"></i>
                                整備記録登録
                            </a>
                        </div>
                        <div class="card-text">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>実施日</th>
                                        <th>内容</th>
                                        <th>費用</th>
                                        <th>備考</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($machinery->maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $maintenance->effective_date->format('Y/m/d') }}</td>
                                        <td>{!! nl2br(e($maintenance->maintenance_details)) !!}</td>
                                        <td class="text-right">{{ number_format($maintenance->cost, 0) }} 円</td>
                                        <td>{!! nl2br(e($maintenance->remarks)) !!}</td>
                                        <td class="text-center">
                                            <form
                                                action="{{ route('maintenances.destroy', $maintenance) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('この整備履歴を削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            履歴なし
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                    <div class="card-title w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>給油記録</h5>
                            <a href="{{ route('machinery-masters.refuels.create', $machinery) }}" class="btn btn-primary btn-sm shadow-sm mb-3">
                                <i class="fas fa-plus mr-1"></i> 給油記録登録
                            </a>
                        </div>
                    </div>
                    <div class="card-text">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>給油日</th>
                                    <th>スタンド名</th>
                                    <th>給油量</th>
                                    <th>給油場所</th>
                                    <th>備考</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($machinery->refuels as $refuel)
                                <tr>
                                    <td>{{ $refuel->refuel_date->format('Y/m/d') }}</td>
                                    <td>{{ $refuel->gas_station }}</td>
                                    <td class="text-right">{{ number_format($refuel->fuel_volume, 2) }} L</td>
                                    <td>{{ $refuel->fuel_location }}</td>
                                    <td>{!! nl2br(e($refuel->remarks)) !!}</td>
                                    <td class="text-center">

                                        <form action="{{ route('refuels.destroy', $refuel) }}" method="POST" onsubmit="return confirm('この給油記録を削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted"> 履歴なし </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                    <div class="card-title w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>稼働履歴</h5>
                            <a href="{{ route('machinery-masters.usages.create', $machinery) }}" class="btn btn-primary btn-sm shadow-sm mb-3">
                                <i class="fas fa-plus mr-1"></i> 稼働履歴登録
                            </a>
                        </div>
                    </div>
                    <div class="card-text">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>利用期間</th>
                                    <th>使用班</th>
                                    <th>指示書番号</th>
                                    <th>現場</th>
                                    <th>開始H</th>
                                    <th>終了H</th>
                                    <th>稼働時間</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($machinery->usages as $usage)
                                <tr>
                                    <td>{{ $usage->start_date?->format('Y/m/d') }} ～ {{ $usage->end_date?->format('Y/m/d') }}</td>
                                    <td>{{ $usage->worker_group_in_use }}</td>
                                    <td>{{ $usage->instruction_number }}</td>
                                    <td>{{ $usage->work_place }}</td>
                                    <td>{{ $usage->hour_meter_start }}</td>
                                    <td>{{ $usage->hour_meter_end }}</td>
                                    <td>
                                        @if($usage->hour_meter_start !== null && $usage->hour_meter_end !== null)
                                        {{ number_format($usage->hour_meter_end - $usage->hour_meter_start, 1) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('usages.destroy', $usage) }}" method="POST" onsubmit="return confirm('この稼働履歴を削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"> <i class="fas fa-trash"></i> </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted"> 履歴なし </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection