@extends('layouts.adminlte')

@section('title', '税率マスタ')

@section('content_header')
<div class="d-flex justify-content-end align-items-center px-2">
    <button type="button" class="btn btn-main btn-sm shadow-sm px-3" data-toggle="modal" data-target="#taxModal" data-mode="create">
        <i class="fas fa-plus"></i> 新規追加
    </button>
</div>
@stop

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>税率一覧 ({{ number_format($taxes->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:0;" class="text-center px-2">ID</th>
                    <th>税率</th>
                    <th>種類</th>
                    <th>状態</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($taxes as $tax)
                <tr>
                    <td style="width:0;" class="text-center px-2">{{ $tax->id }}</td>
                    <td>{{ $tax->name ?? '' }}</td>
                    <td>
                        @if($tax->type)
                        @switch($tax->type)
                        @case('inclusive')
                        内税
                        @break
                        @case('exclusive ')
                        外税
                        @break
                        @default
                        税別
                        @endswitch
                        @endif
                    </td>
                    <td>
                        @if($tax->active==1)
                        <span class="badge badge-success">有効</span>
                        @else
                        <span class="badge badge-secondary">無効</span>
                        @endif
                    </td>
                    <td style="width:0;" class="text-nowrap text-right">
                        {{-- 編集ボタン（データ属性に値を持たせる） --}}
                        <button type="button" class=" btn btn-sm btn-light shadow-sm" title="編集"
                            data-toggle="modal"
                            data-target="#taxModal"
                            data-mode="edit"
                            data-id="{{ $tax->id }}"
                            data-name="{{ $tax->name }}"
                            data-type="{{ $tax->type }}"
                            data-rate="{{ $tax->rate }}"
                            data-active="{{ (int)$tax->active }}"
                            data-update-url="{{ route('master.taxes.update', $tax->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('master.taxes.destroy', $tax->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
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
        {{ $taxes->links() }}
    </div>
</div>

{{-- モーダル（登録・編集共通） --}}
<div class="modal fade" id="taxModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="taxForm" method="POST" action="{{ route('master.taxes.store') }}">
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
                        <label>名称</label>
                        <input type="text" class="form-control" name="name" id="name" required readonly>
                    </div>
                    <div class="form-group">
                        <label>種類</label>
                        <select class="form-control" name="type" id="type" required>
                            <option value="inclusive">内税</option>
                            <option value="exclusive">外税</option>
                            <option value="none">税抜</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>税率</label>
                        <input type="number" class="form-control" name="rate" id="rate">
                    </div>
                    <div class="form-group">
                        <label>状態</label>
                        <select class="form-control" name="active" id="active">
                            <option value="1">有効</option>
                            <option value="0">無効</option>
                        </select>
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
    $(function() {
        // 1. Bootstrapモーダルの表示イベント処理
        $('#taxModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var mode = button.data('mode');
            var modal = $(this);

            if (mode === 'edit') {
                modal.find('#modalTitle').text('税率の編集');
                modal.find('#taxForm').attr('action', button.data('update-url'));
                modal.find('#formMethod').val('PUT');

                modal.find('#name').val(button.data('name'));
                modal.find('#type').val(button.data('type'));
                modal.find('#rate').val(Math.floor(button.data('rate')));
                modal.find('#active').val(String(button.data('active')));
            } else {
                modal.find('#modalTitle').text('税率の追加');
                modal.find('#taxForm').attr('action', "{{ route('master.taxes.store') }}");
                modal.find('#formMethod').val('POST');

                modal.find('#taxForm')[0].reset();
            }
        });

        // 2. typeとrateの変更に応じてnameを自動生成する処理
        const $typeSelect = $('#type');
        const $rateInput = $('#rate');
        const $nameInput = $('#name');

        // typeの選択値を日本語のラベルに変換するマッピング
        const typeLabels = {
            'inclusive': '内税',
            'exclusive': '外税',
            'none': '税抜'
        };

        function updateNameField() {
            const selectedType = $typeSelect.val();
            const rateValue = $rateInput.val();
            const typeLabel = typeLabels[selectedType] || '';

            let rateLabel = '';
            if (rateValue !== '') {
                const parsedRate = Math.floor(Number(rateValue)); // 小数点を切り捨て
                rateLabel = `${parsedRate}%`;
            }

            if (typeLabel || rateLabel) {
                $nameInput.val(`${typeLabel}${rateLabel}`);
            } else {
                $nameInput.val('');
            }
        }

        // jQueryのイベントリスナーを使用することで記述を統一
        $typeSelect.on('change', updateNameField);
        $rateInput.on('input', updateNameField);
    });
</script>
@stop