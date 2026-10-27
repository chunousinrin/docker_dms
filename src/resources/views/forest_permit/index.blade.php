@extends('layouts.adminlte')

@section('title', '許認可管理')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('forest-permit.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規登録
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}

    <button type="button"
        class="close"
        data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif
{{-- 検索パネル --}}
<div class="card search-card mb-4">
    <div class="card-body p-4">
        <form method="GET">
            <div class="form-row">

                <div class="form-group col-md-12">
                    <label class="field-label">キーワード検索</label>
                    <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}" placeholder="施設名称・所在地・申請者・指令番号・整理番号(2-3) など">
                </div>

                <div class="form-group col-md-4">
                    <label class="field-label">申請年月日</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control datepicker text-center border-right-0" name="application_date_from" placeholder="始期" value="{{ request('application_date_from') }}">
                        <div class=" input-group-prepend">
                            <span class="input-group-text bg-white">～</span>
                        </div>
                        <input type="text" class="form-control datepicker text-center" name="application_date_to" placeholder="終期" value="{{ request('application_date_to') }}">
                    </div>
                </div>

                <div class=" form-group col-md-4">
                    <label class="field-label">許可年月日</label>
                    <div class="input-group  input-group-sm">
                        <input type="text" class="form-control datepicker text-center border-right-0" name="permit_date_from" placeholder="始期" value="{{ request('permit_date_from') }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">～</span>
                        </div>
                        <input type="text" class="form-control datepicker text-center" name="permit_date_to" placeholder="終期" value="{{ request('permit_date_to') }}">
                    </div>
                </div>

                <div class="form-group form-group-sm col-md-2">
                    <label class="field-label">保安林種類</label>
                    <select class="form-control select2" name="protection_forest_type">
                        <option value="">選択 / 入力</option>
                        <option value="水かん" {{ old('protection_forest_type', request('protection_forest_type')) == '水かん' ? 'selected' : '' }}>水かん</option>
                        <option value="土流" {{ old('protection_forest_type', request('protection_forest_type')) == '土流' ? 'selected' : '' }}>土流</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label class="field-label">状態</label>

                    <select name="status" class="form-control form-control-sm">
                        <option value="">全て</option>
                        <option value="expired"
                            @selected(request('status')=='expired' )>
                            期限切
                        </option>
                        <option value="warning"
                            @selected(request('status')=='warning' )>
                            期限30日以内
                        </option>
                        <option value="active"
                            @selected(request('status')=='active' )>
                            有効
                        </option>
                    </select>
                </div>
            </div>

            <div class="action-bar d-flex justify-content-between align-items-center">
                <a href="{{ route('forest-permit.index') }}" class="btn-reset btn btn-sm shadow-sm">
                    <i class="fas fa-undo-alt mr-1"></i> 条件をリセット
                </a>
                <div>
                    <a href="{{ route('forest-permit.export.csv', request()->query()) }}" class="btn btn-secondary btn-sm shadow-sm px-4">
                        <i class="fas fa-file-csv mr-1"></i> CSV出力
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm shadow-sm">
                        <i class="fas fa-search mr-2"></i> この条件で絞り込む
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-start align-items-center py-2">
        <a href="{{ route('forest-permit.index', ['status' => 'active']) }}" class="bg-success px-2 font-weight-bold rounded-left shadow-sm">
            <i class="fas fa-check-circle text-white"></i> <span class="text-white">有効 {{ number_format($activeCount) }} 件</span>
        </a>
        <a href="{{ route('forest-permit.index', ['status' => 'warning']) }}" class="bg-orange px-2 font-weight-bold shadow-sm">
            <i class="fas fa-clock text-white"></i> <span class="text-white">期限30日以内 {{ number_format($warningCount) }} 件</span>
        </a>
        <a href="{{ route('forest-permit.index', ['status' => 'expired']) }}" class="bg-danger px-2 font-weight-bold rounded-right shadow-sm">
            <i class="fas fa-exclamation-triangle text-white"></i> <span class="text-white">期限切れ {{ number_format($expiredCount) }} 件</span>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive-custom" style="max-height: 60vh;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>ステータス</th>
                        <th>残日数</th>
                        <th class="text-center">整理番号</th>
                        <th>施設名称</th>
                        <th>保安林種</th>
                        <th>申請者</th>
                        <th class="text-right">許可面積</th>
                        <th>申請年月日</th>
                        <th>許可年月日</th>
                        <th>指令番号</th>
                        <th>許可期間</th>
                        <th>着手日</th>
                        <th>完了日</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                    <tr>
                        <td class="text-center" style="width: 0!important;white-space: nowrap;">
                            @if($application->permit_status === '有効')
                            <span class="badge badge-success">有効</span>

                            @elseif($application->permit_status === '期限間近')
                            <span class="badge badge-warning">期限間近</span>

                            @elseif($application->permit_status === '期限切')
                            <span class="badge badge-danger">期限切</span>

                            @else
                            <span class="badge badge-secondary">未設定</span>
                            @endif
                        </td>{{-- ステータス --}}
                        <td class="text-center font-weight-bold" style="width: 0!important;white-space: nowrap;">
                            @if(!is_null($application->remaining_days))

                            @if($application->remaining_days < 0)
                                <span class="text-danger">
                                {{ $application->remaining_days }}
                                </span>
                                @elseif($application->remaining_days <= 30)
                                    <span class="text-warning">
                                    {{ $application->remaining_days }}
                                    </span>
                                    @else
                                    {{ $application->remaining_days }}
                                    @endif

                                    @endif
                        </td>{{-- 残日数 --}}
                        <td class="text-center"> {{ $application->display_no }} </td>{{-- 整理番号 --}}
                        <td> {{ $application->facility_name }} </td>{{-- 施設名称 --}}
                        <td> {{ $application->protection_forest_type }} </td>{{-- 保安林種 --}}
                        <td> {{ $application->applicant_name }} </td>{{-- 申請者 --}}
                        <td class="text-right"> {{ number_format($application->permit_area, 5) }} </td>{{-- 許可面積 --}}
                        <td> {{ $application->application_date?->format('Y/m/d') }} </td>{{-- 申請年月日 --}}
                        <td> {{ $application->permit_date?->format('Y/m/d') }} </td>{{-- 許可年月日 --}}
                        <td> {{ $application->order_no }} </td>{{-- 指令番号 --}}
                        <td>
                            @if($application->permit_start_date || $application->permit_end_date)
                            {{ $application->permit_start_date?->format('Y/m/d') }}
                            ～
                            {{ $application->permit_end_date?->format('Y/m/d') }}
                            @endif
                        </td>{{-- 許可期間 --}}
                        <td> {{ $application->start_date?->format('Y/m/d') }} </td>{{-- 着手日 --}}
                        <td> {{ $application->completion_date?->format('Y/m/d') }} </td>{{-- 完了日 --}}
                        <td class="text-center" style="width: 0!important;white-space: nowrap;">
                            <a href="{{ route('forest-permit.edit', $application) }}" class="btn btn-sm shadow-sm btn-light" style="padding-left: 0.5em;padding-right:0.2em;">
                                <i class="fas fa-edit" style="color:#343a40"></i>
                            </a>
                            <form method="POST" action="{{ route('forest-permit.destroy', $application) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm shadow-sm btn-light px-2" onclick="return confirm('この申請情報を削除しますか？')">
                                    <i class="fas fa-trash-alt" style="color:#343a40"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="text-center"> データがありません </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- ページャ --}}
    <div class="card-footer clearfix">
        {{ $applications->links() }}
    </div>
</div>

@endsection