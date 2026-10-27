@extends('layouts.adminlte')

@section('title', '重機一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('machinery-masters.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 重機新規登録
    </a>
</div>
@stop

@section('content')
<div class=" card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul"></i> 重機一覧
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive-custom" style="">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th hidden>管理番号</th>
                        <th>重機名</th>
                        <th>ベースマシン</th>
                        <th>メーカー</th>
                        <th>分類</th>
                        <th>状態</th>
                        <th>所有区分</th>
                        <th>アワーメーター</th>
                        <th>装備</th>
                        <th>次回点検日</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machineries as $machinery)
                    <tr>
                        <td hidden> {{ $machinery->id }} </td>
                        <td hidden> {{ $machinery->identification_number }} </td>
                        <td>
                            <a href="{{ route('machinery-masters.show', $machinery) }}" class="text-dark font-weight-bold">
                                <i class="fas fa-angle-double-right mr-2"></i>{{ $machinery->machine_name }} </a>
                        </td>
                        <td> {{ $machinery->base_machine }} </td>
                        <td> {{ $machinery->manufacturer }} </td>
                        <td> {{ $machinery->category?->category_name }} </td>
                        <td>
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
                        </td>
                        <td> {{ $machinery->ownership_label }} </td>
                        <td class="text-right">
                            @if($machinery->current_hour_meter)
                            {{ number_format($machinery->current_hour_meter, 1) }} h
                            @endif
                        </td>
                        <td>
                            @forelse($machinery->attachments as $attachment)
                            <span class="badge bg-light text-dark border">
                                {{ $attachment->model_name }}
                            </span>
                            @empty
                            <span class="text-muted">
                                未設定
                            </span>
                            @endforelse
                        </td>
                        <td>

                            @if($machinery->next_maintenance_date)

                            {{ $machinery->next_maintenance_date->format('Y/m/d') }}

                            @endif

                            <span class="badge bg-{{ $machinery->maintenance_badge_class }}">

                                {{ $machinery->maintenance_badge_text }}

                            </span>

                        </td>
                        <td class="text-center" style="width: 0!important;white-space: nowrap;">
                            <a href="{{ route('machinery-masters.show', $machinery) }}" class="btn btn-xs shadow-sm btn-light" style="padding-left: 0.8em;padding-right:0.8em">
                                <i class="fas fa-info" style="color:#343a40"></i>
                            </a>
                            <a href="{{ route('machinery-masters.edit', $machinery) }}" class="btn btn-xs shadow-sm btn-light" style="padding-left: 0.5em;padding-right:0.2em;">
                                <i class="fas fa-edit" style="color:#343a40"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            データがありません
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- ページャ --}}
    <div class="card-footer clearfix">
    </div>
</div>
@endsection