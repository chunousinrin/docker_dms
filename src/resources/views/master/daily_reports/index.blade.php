@extends('layouts.adminlte')

@section('title', '業務日報マスタ')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <button type="button" class="btn btn-main btn-sm shadow-sm px-3" data-toggle="modal" data-target="#dailyReportModal" data-mode="create">
        <i class="fas fa-plus"></i> 新規追加
    </button>
</div>
@stop

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>業務日報一覧 ({{ number_format($dailyReports->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0 table-responsive-custom">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:0;" class="text-nowrap border-right text-center px-2">ID</th>
                    <th style="width:0;" class="text-nowrap border-right px-3">勤務日</th>
                    <th style="width:0;" class="text-nowrap pl-3 border-right">従業員名</th>
                    <th colspan="3" class="border-right px-3">午前</th>
                    <th colspan="3" class="border-right px-3">午後</th>
                    <th class="border-right px-3">備考</th>
                    <th style="width:0;" class="text-nowrap text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyReports as $r)
                <tr>
                    <td style="width:0;" class="text-nowrap border-right text-center px-2">{{ $r->id }}</td>
                    <td style="width:0;" class="text-nowrap border-right px-3"> {{ $r->work_date->format('Y-m-d') }} </td>
                    <td class="border-right">{{ $r->user->name }}</td>
                    <td style="width:0;" class="text-nowrap pl-3">
                        @if($r->amWeather)
                        @switch($r->amWeather->name)
                        @case('晴')
                        <i class="fas fa-sun text-warning" title="晴れ"></i>
                        @break
                        @case('曇')
                        <i class="fas fa-cloud text-secondary" title="曇り"></i>
                        @break
                        @case('雨')
                        <i class="fas fa-umbrella text-primary" title="雨"></i>
                        @break
                        @case('雷')
                        <i class="fas fa-bolt text-warning" title="雷"></i>
                        @break
                        @case('雪')
                        <i class="fa-regular fa-snowflake text-info" title="雪"></i>
                        @break
                        @default
                        {{ $r->amWeather->name }}
                        @endswitch
                        @else
                        -
                        @endif
                    </td>
                    <td style="width:0;" class="text-nowrap">{{ $r->amBusinessType->name  }}</td>
                    <td style="width:0;" class="text-nowrap border-right pr-3">{{ Str::limit($r->am_summary, 20, '…') }}</td>
                    <td style="width:0;" class="text-nowrap pl-3">
                        @if($r->pmWeather)
                        @switch($r->pmWeather->name)
                        @case('晴')
                        <i class="fas fa-sun text-warning" title="晴れ"></i>
                        @break
                        @case('曇')
                        <i class="fas fa-cloud text-secondary" title="曇り"></i>
                        @break
                        @case('雨')
                        <i class="fas fa-umbrella text-primary" title="雨"></i>
                        @break
                        @case('雷')
                        <i class="fas fa-bolt text-warning" title="雷"></i>
                        @break
                        @case('雪')
                        <i class="fa-regular fa-snowflake text-info" title="雪"></i>
                        @break
                        @default
                        {{ $r->pmWeather->name }}
                        @endswitch
                        @else
                        -
                        @endif
                    </td>
                    <td style="width:0;" class="text-nowrap">{{ $r->pmBusinessType->name }}</td>
                    <td style="width:0;" class="text-nowrap border-right pr-3">{{ Str::limit($r->pm_summary, 20, '…') }}</td>
                    <td class="border-right px-3">{{ Str::limit($r->remarks, 25, '…') }}</td>
                    <td style="width:0;" class="text-nowrap text-right">
                        {{-- 編集ボタン（データ属性に値を持たせる） --}}
                        <button type="button" class="btn btn-sm btn-light shadow-sm" title="編集"
                            data-toggle="modal"
                            data-target="#dailyReportModal"
                            data-mode="edit"
                            data-id="{{ $r->id }}"
                            data-work_date="{{ $r->work_date->format('Y-m-d') }}"
                            data-user_id="{{ $r->user_id }}"
                            data-am_business_type_id="{{ $r->am_business_type_id }}"
                            data-am_summary="{{ $r->am_summary }}"
                            data-am_weather_id="{{ $r->am_weather_id }}"
                            data-pm_business_type_id="{{ $r->pm_business_type_id }}"
                            data-pm_summary="{{ $r->pm_summary }}"
                            data-pm_weather_id="{{ $r->pm_weather_id }}"
                            data-remarks="{{ $r->remarks }}"
                            data-update-url="{{ route('master.daily-reports.update', $r->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('master.daily-reports.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light px-2 shadow-sm" title="削除"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $dailyReports->links() }}
    </div>

</div>

{{-- モーダル（登録・編集共通） --}}
<div class="modal fade" id="dailyReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="dailyReportForm" method="POST" action="{{ route('master.daily-reports.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">業務日報の追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>勤務日</label>
                        <input type="text" class="form-control datepicker" name="work_date" id="work_date" required>
                    </div>
                    <div class="form-group">
                        <label>氏名</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">選択してください</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>午前業種</label>
                        <select name="am_business_type_id" id="am_business_type_id" class="form-control" required>
                            <option value="">選択してください</option>
                            @foreach($businessTypes as $type)
                            <option value="{{ $type->id }}" {{ old('am_business_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>午前摘要</label>
                        <input type="text" class="form-control" name="am_summary" id="am_summary">
                    </div>
                    <div class="form-group">
                        <label>午前天気</label>
                        <select name="am_weather_id" id="am_weather_id" class="form-control" required>
                            <option value="">選択してください</option>
                            @foreach($weathers as $weather)
                            <option value="{{ $weather->id }}" {{ old('am_weather_id') == $weather->id ? 'selected' : '' }}>
                                {{ $weather->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>午後業種</label>
                        <select name="pm_business_type_id" id="pm_business_type_id" class="form-control" required>
                            <option value="">選択してください</option>
                            @foreach($businessTypes as $type)
                            <option value="{{ $type->id }}" {{ old('pm_business_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>午後摘要</label>
                        <input type="text" class="form-control" name="pm_summary" id="pm_summary">
                    </div>
                    <div class="form-group">
                        <label>午後天気</label>
                        <select name="pm_weather_id" id="pm_weather_id" class="form-control" required>
                            <option value="">選択してください</option>
                            @foreach($weathers as $weather)
                            <option value="{{ $weather->id }}" {{ old('pm_weather_id') == $weather->id ? 'selected' : '' }}>
                                {{ $weather->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>備考</label>
                        <input type="text" class="form-control" name="remarks" id="remarks">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">保存する</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $('#dailyReportModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var mode = button.data('mode');
        var modal = $(this);

        if (mode === 'edit') {
            modal.find('#modalTitle').text('業務日報の編集');
            modal.find('#dailyReportForm').attr('action', button.data('update-url'));
            modal.find('#formMethod').val('PUT');

            modal.find('#work_date').val(button.data('work_date'));
            modal.find('#user_id').val(button.data('user_id'));
            modal.find('#am_business_type_id').val(button.data('am_business_type_id'));
            modal.find('#am_summary').val(button.data('am_summary'));
            modal.find('#am_weather_id').val(button.data('am_weather_id'));
            modal.find('#pm_business_type_id').val(button.data('pm_business_type_id'));
            modal.find('#pm_summary').val(button.data('pm_summary'));
            modal.find('#pm_weather_id').val(button.data('pm_weather_id'));
            modal.find('#remarks').val(button.data('remarks'));
        } else {
            modal.find('#modalTitle').text('業務日報の追加');
            modal.find('#dailyReportForm').attr('action', "{{ route('master.daily-reports.store') }}");
            modal.find('#formMethod').val('POST');

            modal.find('#dailyReportForm')[0].reset();
        }
    });
</script>
@stop