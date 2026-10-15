@extends('layouts.adminlte')

@section('title', '銀行口座マスタ')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <button type="button" class="btn btn-main btn-sm shadow-sm px-3" data-toggle="modal" data-target="#bankModal" data-mode="create">
        <i class="fas fa-plus"></i> 新規追加
    </button>
</div>
@stop

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>登録口座一覧 ({{ number_format($banks->total()) }} 件)
        </h5>
    </div>

    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>銀行名</th>
                    <th>支店名</th>
                    <th>口座種別</th>
                    <th>口座番号</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banks as $bank)
                <tr>
                    <td>{{ $bank->id }}</td>
                    <td>{{ $bank->bank_name }}</td>
                    <td>{{ $bank->branch_name }}</td>
                    <td>{{ $bank->account_type }}</td>
                    <td>{{ $bank->account_number }}</td>
                    <td style="width:0;" class="text-nowrap text-right">
                        {{-- 編集ボタン（データ属性に値を持たせる） --}}
                        <button type="button" class="btn btn-sm btn-light shadow-sm" title="編集"
                            data-toggle="modal"
                            data-target="#bankModal"
                            data-mode="edit"
                            data-id="{{ $bank->id }}"
                            data-bank_name="{{ $bank->bank_name }}"
                            data-branch_name="{{ $bank->branch_name }}"
                            data-account_type="{{ $bank->account_type }}"
                            data-account_number="{{ $bank->account_number }}"
                            data-update-url="{{ route('master.banks.update', $bank->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('master.banks.destroy', $bank->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light px-2 shadow-sm" title="削除">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $banks->links() }}
    </div>
</div>

{{-- モーダル（登録・編集共通） --}}
<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="bankForm" method="POST" action="{{ route('master.banks.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">銀行口座の追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>銀行名</label>
                        <input type="text" class="form-control" name="bank_name" id="bank_name" required>
                    </div>
                    <div class="form-group">
                        <label>支店名</label>
                        <input type="text" class="form-control" name="branch_name" id="branch_name" required>
                    </div>
                    <div class="form-group">
                        <label>口座種別</label>
                        <select class="form-control" name="account_type" id="account_type">
                            <option value="普通">普通</option>
                            <option value="当座">当座</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>口座番号</label>
                        <input type="text" class="form-control" name="account_number" id="account_number" required>
                    </div>
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
    $('#bankModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var mode = button.data('mode');
        var modal = $(this);

        if (mode === 'edit') {
            modal.find('#modalTitle').text('銀行口座の編集');
            modal.find('#bankForm').attr('action', button.data('update-url'));
            modal.find('#formMethod').val('PUT');

            modal.find('#bank_name').val(button.data('bank_name'));
            modal.find('#branch_name').val(button.data('branch_name'));
            modal.find('#account_type').val(button.data('account_type'));
            modal.find('#account_number').val(button.data('account_number'));
        } else {
            modal.find('#modalTitle').text('銀行口座の追加');
            modal.find('#bankForm').attr('action', "{{ route('master.banks.store') }}");
            modal.find('#formMethod').val('POST');

            modal.find('#bankForm')[0].reset();
        }
    });
</script>
@stop