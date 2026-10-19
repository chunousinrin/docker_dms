@extends('layouts.adminlte')

@section('title', '請求書作成')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>請求書作成</h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 請求書一覧
    </a>
</div>
@stop

@section('content')
<div class="container-fluid">
    <form id="invoice-form" action="{{ route('invoices.store') }}" method="POST">
        @csrf
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @isset($estimate)
        <input type="hidden" name="estimate_id" value="{{ $estimate->id }}">

        @if(isset($existingInvoices) && $existingInvoices->isNotEmpty())
        <div class="alert alert-warning">
            この見積からは、すでに請求書が作成されています（請求番号：{{ $existingInvoices->implode('、') }}）。
            二重請求にならないかご確認ください。
        </div>
        @endif
        @endisset
        @endif
        <div class="row">
            {{-- 左カラム：基本情報・顧客情報 --}}
            <div class="col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">請求書基本情報</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">請求番号</label>
                                <input type="text" name="invoice_number" class="form-control" value="{{ $suggestedNumber }}" required readonly>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">事業分類 <span class="text-danger">*</span></label>
                                <select name="invoice_category_id" class="form-control required_field" required>
                                    <option value="" hidden selected>-- 選択してください --</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected( old( 'invoice_category_id' , $estimate->estimate_category_id ?? null ) == $cat->id )>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <div class="justify-content-between align-items-center d-flex">
                                    <label class="font-weight-bold text-dark mb-1 small">発行日 <span class="text-danger">*</span></label>
                                    <label class="font-weight-bold text-dark mb-1 small">(表示有無)</label>
                                </div>
                                <div class="input-group">
                                    <input type="input" name="billing_date" class="form-control datepicker required_field" value="{{ date('Y-m-d') }}" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="hidden" name="show_billing_date" value="0">
                                            <input type="checkbox" name="show_billing_date" value="1" checked title="印字する">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">顧客名 <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="customer_name" class="form-control required_field" required
                                        value="{{ old('customer_name', $estimate->customer_name ?? '') }}">
                                    <div style="width: 100px;" class="input-group-append">
                                        <select name="honorific" id="honorific-input" class="form-control select2">
                                            <option value="様" @selected( old( 'honorific' , $estimate->honorific ?? '様' ) == '様' )>様</option>
                                            <option value="御中" @selected( old( 'honorific' , $estimate->honorific ?? '' ) == '御中' )>御中</option>
                                            <option value="殿" @selected( old( 'honorific' , $estimate->honorific ?? '' ) == '殿' )>殿</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">事業ID (ProjectID)</label>
                                <input type="number" name="project_id" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">事業名 / 件名</label>
                                <input type="text" name="project_name" class="form-control"
                                    value="{{ old('project_name', $estimate->project_name ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark mb-1 small">場所 (Project Location)</label>
                                <input type="text" name="project_location" class="form-control"
                                    value="{{ old('project_location', $estimate->project_location ?? '') }}">
                            </div>
                            <div class="col-md-6 form-group ">
                                <label class="font-weight-bold text-dark mb-1 small">工事・事業期間</label>
                                <div class="input-group">
                                    <input type="input" name="completion_start_date" class="form-control datepicker">
                                    <div class="input-group-append"><span class="input-group-text">～</span></div>
                                    <input type="input" name="completion_end_date" class="form-control datepicker border-left-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 明細テーブル --}}
                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0" id="items-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 0; white-space: nowrap;" class="text-center align-middle p-2"><i class="fas fa-sort"></i></th>
                                    <th style="width: 0; white-space: nowrap;">#</th>
                                    <th>内容 / 品名</th>
                                    <th style="width: 12%">数量</th>
                                    <th style="width: 12%">単位</th>
                                    <th style="width: 12%">単価</th>
                                    <th style="width: 15%">金額</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-items">
                                @php
                                $items = collect( old( 'items', $estimate->items ?? [] ) )->values();
                                @endphp

                                @for($i = 0; $i < 15; $i++)

                                    @php
                                    $item=$items[$i] ?? null;
                                    @endphp

                                    <tr class="item-row">
                                    <td class="text-center align-middle p-2" style="cursor: grab;">
                                        <i class="fas fa-bars"></i>
                                    </td>
                                    <td class="text-center align-middle row-number p-0">{{ $i+1 }}</td>
                                    <td><input type="text" name="items[{{ $i }}][description]" value="{{ data_get($item, 'description') }}" class="form-control form-control-sm item-name"></td>
                                    <td><input type="number" step="0.01" name="items[{{$i}}][quantity]" value="{{ data_get($item, 'quantity') }}" class="form-control form-control-sm item-qty text-right quantity"></td>
                                    <td><input type="text" name="items[{{$i}}][unit]" value="{{ data_get($item, 'unit') }}" class="form-control form-control-sm unit"></td>
                                    <td><input type="number" step="0.01" name="items[{{$i}}][unit_price]" value="{{ data_get($item, 'unit_price') }}" class="form-control form-control-sm item-price unit-price text-right"></td>
                                    <td><input type="text" class="form-control form-control-sm item-subtotal text-right" readonly tabindex="-1"></td>
                                    <input type="hidden" name="items[{{$i}}][order_number]" class="item-order" value="{{ $i + 1 }}">
                                    </tr>
                                    @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 右カラム：設定・銀行・管理 --}}
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">管理・支払設定</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="justify-content-between align-items-center d-flex">
                                <label class="font-weight-bold text-dark mb-1 small">作成者</label>
                                <label class="font-weight-bold text-dark mb-1 small">(表示有無)</label>
                            </div>
                            <div class="input-group">
                                <div class="form-control bg-light">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="hidden" name="show_creator" value="0">
                                        <input type="checkbox" name="show_creator" value="1" checked title="印字する">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">発行者 (支所等) <span class="text-danger">*</span></label>
                            <select name="requester_id" class="form-control required_field" required>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $branch->name }} ({{ $branch->representative }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">税率設定 <span class="text-danger">*</span></label>
                            <select name="tax_type_id" id="tax_type_id" class="form-control required_field">
                                @foreach($taxTypes as $tax)
                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}" data-type="{{ $tax->type }}"
                                    @selected( old( 'tax_type_id' , $estimate->tax_type_id ?? null ) == $tax->id ) >
                                    {{ $tax->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">支払期限</label>
                            <input type="input" name="due_date" class="form-control mb-2 datepicker" placeholder="未入力の場合「発行日から45日」を自動設定">
                        </div>

                        <hr>
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">振込先指定</label>
                            @for($j = 1; $j <= 3; $j++)
                                <div class="input-group mb-1 ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ $j }}.</span>
                                </div>
                                <select name="bank_id{{ $j }}" class="form-control">
                                    <option value="">-- 口座{{ $j }} (未選択) --</option>
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ $bank->id == $j ? 'selected' : '' }}>
                                        {{ $bank->bank_name }} {{ $bank->branch_name }} {{ $bank->account_type }} {{ $bank->account_number }}
                                    </option>
                                    @endforeach
                                </select>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">適用 (Remarks)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="請求書に印字されます"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">社内メモ (Memo)</label>
                        <textarea name="memo" class="form-control" rows="2"
                            placeholder="社内管理用（印字されません）">@isset($estimate->estimate_number)見積番号：{{ $estimate->estimate_number }}@endisset</textarea>
                    </div>

                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between">
                            <span>小計:</span>
                            <span id="calc-subtotal">¥0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>消費税:</span>
                            <span id="calc-tax">¥0</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>税込合計:</span>
                            <span id="calc-total" class="text-primary">¥0</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" id="preview-btn" class="btn btn-sm btn-secondary btn-block mb-2 shadow-sm">
                        <i class="far fa-file-pdf mr-1"></i> PDFプレビュー
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm">
                        <i class="fas fa-save mr-1"></i> 請求書を保存
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 3. 計算ロジック ---
        // 浮動小数点の微小誤差を除く
        const fix = (x, d) => Number(x.toFixed(d));

        window.calculate = function() {
            let subtotal = 0;

            $('#items-table tbody tr').each(function() {
                const qty = parseFloat($(this).find('.item-qty').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                const lineTotal = qty * price; // 行ごとの丸めはしない

                $(this).find('.item-subtotal').val(lineTotal.toLocaleString());
                subtotal += lineTotal;
            });
            subtotal = fix(subtotal, 4);

            const taxOption = $('#tax_type_id option:selected');
            const rate = parseFloat(taxOption.data('rate')) || 0; // 0.1 = 10%
            const type = taxOption.data('type');

            let tax = 0;
            let total = Math.floor(subtotal);

            if (type === 'exclusive') {
                total = Math.floor(fix(subtotal * (1 + rate), 6));
                tax = Math.floor(fix(subtotal * rate, 6));
            } else if (type === 'inclusive') {
                tax = Math.floor(fix(total * rate / (1 + rate), 6));
            }

            // 小計の表示は PDF の「税抜 = 合計 − 税額」と揃える
            $('#calc-subtotal').text('¥' + (type === 'exclusive' ? total - tax : total).toLocaleString());
            $('#calc-tax').text('¥' + tax.toLocaleString());
            $('#calc-total').text('¥' + total.toLocaleString());
        };

        // --- 4. 行番号と順序の更新 ---
        window.updateRowNumbers = function() {

            $('#sortable-items tr').each(function(index) {
                const no = index + 1;
                // 表示番号
                $(this).find('.row-number').text(no);
                // hidden
                $(this).find('.item-order').val(no).attr('name', `items[${no}][order_number]`);
                // 品名
                $(this).find('.item-name').attr('name', `items[${no}][description]`);
                // 数量
                $(this).find('.quantity').attr('name', `items[${no}][quantity]`);
                // 単位
                $(this).find('[name$="[unit]"]').attr('name', `items[${no}][unit]`);
                // 単価
                $(this).find('.unit-price').attr('name', `items[${no}][unit_price]`);
            });
        };

        // --- 5. イベント監視 ---
        $(document).on('input change', '.item-qty, .item-price, #tax_type_id', function() {
            calculate();
        });

        // --- 6. プレビュー実行（非同期Blob生成パターン） ---
        let previewLoading = false;

        async function previewPdf() {
            if (previewLoading) return;
            previewLoading = true;

            const $btn = $('#preview-btn');
            const originalHtml = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> 生成中...').prop('disabled', true);

            const form = document.getElementById('invoice-form');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('invoices.preview') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json', // 検証エラーを302ではなく422で受け取る
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const contentType = response.headers.get('content-type') || '';

                // 入力検証エラー
                if (response.status === 422) {
                    const json = await response.json();
                    throw new Error(Object.values(json.errors).flat().join('\n'));
                }
                // サーバーエラー
                if (!response.ok) {
                    const j = await response.clone().json().catch(() => null);
                    console.error('preview error', response.status, j);
                    throw new Error(j?.message ? j.message : 'サーバーエラー (' + response.status + ')');
                }
                // PDF以外が返った場合
                if (response.redirected || !contentType.includes('application/pdf')) {
                    throw new Error('PDF以外のレスポンスが返りました (' + contentType + ')');
                }

                // PDFをBlob化し、ブラウザ内だけで有効な一時URLを発行
                const blob = await response.blob();
                const pdfUrl = URL.createObjectURL(blob);

                Fancybox.show([{
                    src: pdfUrl,
                    type: 'pdf'
                }], {
                    on: {
                        destroy: () => URL.revokeObjectURL(pdfUrl) // 閉じたら一時URLを解放
                    }
                });

            } catch (e) {
                console.error(e);
                alert(e.message || 'PDFプレビューの生成に失敗しました。入力内容を確認してください。');
            } finally {
                previewLoading = false;
                $btn.html(originalHtml).prop('disabled', false);
            }
        }

        // イベントリスナーの紐付け
        $('#preview-btn').on('click', function(e) {
            e.preventDefault(); // 念のためデフォルト挙動をリセット
            previewPdf();
        });

        // スタイル動的追加
        $("<style>")
            .prop("type", "text/css")
            .html(`
                .ui-state-highlight { height: 45px; background-color: #fcf8e3; border: 1px dashed #fbeed5; }
                .item-row.ui-sortable-helper { background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: table; width: 100%; }
            `)
            .appendTo("head");

        // 初回計算
        calculate();
    });
</script>
@endpush