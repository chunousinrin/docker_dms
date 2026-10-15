@extends('layouts.adminlte')

@section('title', '事業分類マスタ')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <button type="button" class="btn btn-main btn-sm shadow-sm px-3" data-toggle="modal" data-target="#classificationModal" data-mode="create">
        <i class="fas fa-plus"></i> 新規追加
    </button>
</div>
@stop

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>事業分類一覧 ({{ number_format($classifications->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:0;" class="text-center px-2">ID</th>
                    <th>事業分類名</th>
                    <th>説明</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classifications as $classification)
                <tr>
                    <td style="width:0;" class="text-center px-2">{{ $classification->id }}</td>
                    <td>{{ $classification->name }}</td>
                    <td>{{ $classification->description }}</td>
                    <td style="width:0;" class="text-nowrap text-right">
                        {{-- 編集ボタン（データ属性に値を持たせる） --}}
                        <button type="button" class=" btn btn-sm btn-light shadow-sm" title="編集"
                            data-toggle="modal"
                            data-target="#classificationModal"
                            data-mode="edit"
                            data-id="{{ $classification->id }}"
                            data-name="{{ $classification->name }}"
                            data-description="{{ $classification->description }}"
                            data-update-url="{{ route('master.classifications.update', $classification->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('master.classifications.destroy', $classification->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
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
        {{ $classifications->links() }}
    </div>
</div>

{{-- モーダル（登録・編集共通） --}}
<div class="modal fade" id="classificationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="classificationForm" method="POST" action="{{ route('master.classifications.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">事業分類の追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>事業分類名</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>説明</label>
                        <input type="text" class="form-control" name="description" id="description">
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
    $('#classificationModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var mode = button.data('mode');
        var modal = $(this);

        if (mode === 'edit') {
            modal.find('#modalTitle').text('事業分類の編集');
            modal.find('#classificationForm').attr('action', button.data('update-url'));
            modal.find('#formMethod').val('PUT');

            modal.find('#name').val(button.data('name'));
            modal.find('#description').val(button.data('description'));
        } else {
            modal.find('#modalTitle').text('事業分類の追加');
            modal.find('#classificationForm').attr('action', "{{ route('master.classifications.store') }}");
            modal.find('#formMethod').val('POST');

            modal.find('#classificationForm')[0].reset();
        }
    });
</script>
@stop