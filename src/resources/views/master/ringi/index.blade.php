@extends('layouts.adminlte')

@section('title', '稟議種類マスタ')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <button type="button" class="btn btn-main btn-sm shadow-sm px-3" data-toggle="modal" data-target="#ringiTypeModal" data-mode="create">
        <i class="fas fa-plus"></i> 新規追加
    </button>
</div>
@stop

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>稟議種類一覧 ({{ number_format($ringiTypes->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:0;" class="text-center px-2">ID</th>
                    <th>稟議種類名</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ringiTypes as $ringiType)
                <tr>
                    <td style="width:0;" class="text-center px-2">{{ $ringiType->id }}</td>
                    <td>{{ $ringiType->name }}</td>
                    <td style="width:0;" class="text-nowrap text-right">
                        {{-- 編集ボタン（データ属性に値を持たせる） --}}
                        <button type="button" class=" btn btn-sm btn-light shadow-sm" title="編集"
                            data-toggle="modal"
                            data-target="#ringiTypeModal"
                            data-mode="edit"
                            data-id="{{ $ringiType->id }}"
                            data-name="{{ $ringiType->name }}"
                            data-update-url="{{ route('master.ringi.update', $ringiType->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('master.ringi.destroy', $ringiType->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
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
        {{ $ringiTypes->links() }}
    </div>
</div>

{{-- モーダル（登録・編集共通） --}}
<div class="modal fade" id="ringiTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="ringiTypeForm" method="POST" action="{{ route('master.ringi.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">稟議種類の追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>稟議種類名</label>
                        <input type="text" class="form-control" name="name" id="name" required>
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
    $('#ringiTypeModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var mode = button.data('mode');
        var modal = $(this);

        if (mode === 'edit') {
            modal.find('#modalTitle').text('稟議種類の編集');
            modal.find('#ringiTypeForm').attr('action', button.data('update-url'));
            modal.find('#formMethod').val('PUT');

            modal.find('#name').val(button.data('name'));
        } else {
            modal.find('#modalTitle').text('稟議種類の追加');
            modal.find('#ringiTypeForm').attr('action', "{{ route('master.ringi.store') }}");
            modal.find('#formMethod').val('POST');

            modal.find('#ringiTypeForm')[0].reset();
        }
    });
</script>
@stop