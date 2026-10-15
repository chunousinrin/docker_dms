@extends('layouts.adminlte')

@section('title', '棚卸実施')

@section('content_header')
<h1>棚卸実施</h1>
@stop

@section('content')
@include('stamps.partials.flash')

<form method="POST" action="{{ route('stamps.audits.store') }}" id="audit-form"
    onsubmit="return confirm('棚卸結果を登録します。差異がある場合は帳簿を実数に合わせる調整履歴が自動作成されます。よろしいですか？');">
    @csrf

    <div class="card card-outline card-primary">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="audited_on">実施日 <span class="text-danger">*</span></label>
                    <input type="date" name="audited_on" id="audited_on" required
                        value="{{ old('audited_on', now()->format('Y-m-d')) }}"
                        max="{{ now()->format('Y-m-d') }}"
                        class="form-control @error('audited_on') is-invalid @enderror">
                    @error('audited_on') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-9">
                    <label for="memo">メモ</label>
                    <input type="text" name="memo" id="memo" maxlength="255" value="{{ old('memo') }}"
                        class="form-control @error('memo') is-invalid @enderror"
                        placeholder="例: 2026年度 第3四半期 定例棚卸">
                    @error('memo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">実数入力（帳簿数は現在の値です。差異がある場合は理由が必須）</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th>品目</th>
                        <th class="text-right">額面</th>
                        <th class="text-right">帳簿数</th>
                        <th style="width:140px">実数</th>
                        <th class="text-right">差異</th>
                        <th class="text-right">差異金額</th>
                        <th style="min-width:240px">差異の理由</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                    <tr class="audit-row" data-book="{{ $item->stock }}" data-face="{{ $item->face_value }}">
                        <td>
                            {{ $item->name }}
                            <input type="hidden" name="lines[{{ $i }}][stamp_item_id]" value="{{ $item->id }}">
                        </td>
                        <td class="text-right">¥{{ number_format($item->face_value) }}</td>
                        <td class="text-right">{{ number_format($item->stock) }}</td>
                        <td>
                            <input type="number" min="0" required
                                name="lines[{{ $i }}][counted_qty]"
                                value="{{ old("lines.$i.counted_qty") }}"
                                class="form-control form-control-sm counted @error(" lines.$i.counted_qty") is-invalid @enderror">
                        </td>
                        <td class="text-right diff">-</td>
                        <td class="text-right diff-amount">-</td>
                        <td>
                            <input type="text" maxlength="255"
                                name="lines[{{ $i }}][note]"
                                value="{{ old("lines.$i.note") }}"
                                class="form-control form-control-sm note @error(" lines.$i.note") is-invalid @enderror">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="5" class="text-right">差異金額合計</td>
                        <td class="text-right" id="total-diff-amount">¥0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">棚卸結果を登録</button>
            <a href="{{ route('stamps.audits.index') }}" class="btn btn-default">戻る</a>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    (function() {
        const yen = n => (n < 0 ? '-' : '') + '¥' + Math.abs(n).toLocaleString();
        const sign = n => (n > 0 ? '+' : '') + n.toLocaleString();

        function recalc() {
            let total = 0;
            document.querySelectorAll('.audit-row').forEach(row => {
                const input = row.querySelector('.counted');
                const note = row.querySelector('.note');
                const diffC = row.querySelector('.diff');
                const amtC = row.querySelector('.diff-amount');

                if (input.value === '') {
                    diffC.textContent = '-';
                    amtC.textContent = '-';
                    note.required = false;
                    row.classList.remove('table-warning');
                    return;
                }
                const diff = parseInt(input.value, 10) - parseInt(row.dataset.book, 10);
                const amt = diff * parseInt(row.dataset.face, 10);
                total += amt;

                diffC.textContent = sign(diff);
                amtC.textContent = yen(amt);
                diffC.className = 'text-right diff ' + (diff === 0 ? '' : 'text-danger font-weight-bold');
                note.required = diff !== 0;
                row.classList.toggle('table-warning', diff !== 0);
            });
            document.getElementById('total-diff-amount').textContent = yen(total);
        }

        document.querySelectorAll('.counted').forEach(el => el.addEventListener('input', recalc));
        recalc();
    })();
</script>
@stop