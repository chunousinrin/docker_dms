@extends('layouts.adminlte')

@section('title', '見積作成')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>見積書作成</h1>
    <a href="{{ route('estimates.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 見積書一覧
    </a>
</div>
@stop

@section('content')
<form action="{{ route('estimates.store') }}" method="POST" id="estimate-form">
    @csrf
    <div class="row">
        {{-- 左カラム：基本情報・顧客情報 --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">見積書基本情報</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">見積番号</label>
                            <input type="text" name="estimate_number" class="form-control" required value="{{ $suggestedNumber }}" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業分類 <span class="text-danger">*</span></label>
                            <select name="estimate_category_id" class="form-control required_field" required>
                                <option value="" hidden selected>-- 選択してください --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <div class="justify-content-between align-items-center d-flex">
                                <label class="font-weight-bold text-dark mb-1 small">発行日 <span class="text-danger">*</span></label>
                                <label class="font-weight-bold text-dark mb-1 small">(表示有無)</label>
                            </div>
                            <div class="input-group">
                                <input type="text" name="created_date" class="form-control datepicker required_field" value="{{ now()->format('Y-m-d') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="hidden" name="show_estimate_date" value="0">
                                        <input type="checkbox" name="show_estimate_date" value="1" checked title="印字する">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">顧客名 <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="customer_name" class="form-control required_field" required>
                                <div style="width: 100px;">
                                    <select type="text" name="honorific" id="honorific-input" class="form-control select2 required_field" required>
                                        <option value="様" @selected( old('honorific')=='様' )>様</option>
                                        <option value="御中" @selected( old('honorific')=='御中' )>御中</option>
                                        <option value="殿" @selected( old('honorific')=='殿' )>殿</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業ID (ProjectID)</label>
                            <input type="text" name="project_id" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業名 / 件名</label>
                            <input type="text" name="project_name" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">場所 (Project Location)</label>
                            <input type="text" name="project_location" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark mb-1 small">実施予定日</label>
                            <input type="text" name="scheduled_date" class="form-control datepicker">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 明細テーブル --}}
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered">
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
                            @for($i = 0; $i < 15; $i++)
                                <tr class="item-row">
                                <td class="text-center align-middle p-2" style="cursor: grab;">
                                    <i class="fas fa-bars"></i>
                                    <input type="hidden" name="items[{{ $i }}][order_number]" class="item-order" value="{{ $i + 1 }}">
                                </td>
                                <td class="text-center align-middle row-number p-0">{{ $i + 1 }}</td>
                                <td><input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm "></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control form-control-sm text-right quantity"></td>
                                <td><input type="text" name="items[{{ $i }}][unit]" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm text-right unit-price"></td>
                                <td class="text-right align-middle"><input type="text" class="form-control form-control-sm text-right amount-cell" readonly tabindex="-1"></td>
                                </tr>
                                @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 右カラム：管理・期限・メモ設定 --}}
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">管理・期限設定</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="justify-content-between align-items-center d-flex">
                            <label class="font-weight-bold text-dark mb-1 small">作成者</label>
                            <label class="font-weight-bold text-dark mb-1 small"> (表示有無)</label>
                        </div>
                        <div class="input-group">
                            <div class="form-control bg-light">
                                {{ Auth::user()->name }}
                            </div>
                            <input type="hidden" name="creator_id" value="{{ Auth::id() }}">
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
                        <select name="tax_type_id" id="tax_type_id" class="form-control required_field" required>
                            @foreach($taxTypes as $taxType)
                            <option value="{{ $taxType->id }}" data-type="{{ $taxType->type }}" data-rate="{{ $taxType->rate }}">
                                {{ $taxType->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">金額表示設定</label>
                        <select name="unit_price_estimate" class="form-control">
                            <option value="1" selected>表示</option>
                            <option value="0">非表示(単価見積)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">見積有効期限</label>
                        <input type="text" name="effective_date" class="form-control datepicker" placeholder="未記入の場合「発行日より45日以内」が自動設定">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">適用 (Remarks)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="見積書に印字されます"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">社内メモ (Memo)</label>
                        <textarea name="memo" class="form-control" rows="2" placeholder="社内管理用（印字されません）"></textarea>
                    </div>

                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between">
                            <span>小計:</span>
                            <span id="subtotal">¥0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>消費税:</span>
                            <span id="tax_amount">¥0</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>税込合計:</span>
                            <span id="total_amount" class="text-primary">¥0</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" id="preview-btn" class="btn btn-sm btn-secondary btn-block mb-2 shadow-sm"> <i class="far fa-file-pdf"></i>
                        PDFプレビュー
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm">
                        <i class="fas fa-save"></i> 見積書を保存
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- number input ホイール事故対策 ---
        document.addEventListener('wheel', function() {
            if (
                document.activeElement &&
                document.activeElement.type === 'number'
            ) {
                document.activeElement.blur();
            }
        }, {
            passive: true
        });

        // --- Sortable 行番号更新 ---
        window.updateRowNumbers = function() {
            $('#sortable-items tr').each(function(index) {
                const newIndex = index + 1;
                $(this).find('.row-number').text(newIndex);
                $(this).find('.item-order').val(newIndex);
            });
        };

        // --- 金額計算 ---
        function calculateTotals() {
            let subtotal = 0;

            document.querySelectorAll('.item-row').forEach(function(row) {
                const quantity =
                    parseFloat(
                        row.querySelector('.quantity').value
                    ) || 0;

                const unitPrice =
                    parseFloat(
                        row.querySelector('.unit-price').value
                    ) || 0;

                // --- 明細金額（丸めなし） ---
                const amount = quantity * unitPrice;

                // --- 行金額表示 ---
                row.querySelector('.amount-cell').value =
                    amount.toLocaleString('ja-JP', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });

                // --- 小計加算 ---
                subtotal += amount;
            });

            // --- 税率取得 ---
            const taxSelect = document.getElementById('tax_type_id');
            const selectedOption = taxSelect.options[taxSelect.selectedIndex];
            const taxType = selectedOption.dataset.type;
            const taxRate = parseFloat(selectedOption.dataset.rate);

            // --- 消費税計算（丸めなし） ---
            let taxAmount = 0;

            if (taxType === 'exclusive') {
                taxAmount = subtotal * (taxRate / 100);
            } else if (taxType === 'inclusive') {
                taxAmount =
                    subtotal -
                    (
                        subtotal /
                        (1 + (taxRate / 100))
                    );
            }

            // --- 合計の計算（外税なら足す、内税なら小計のまま） ---
            let total = 0;
            if (taxType === 'exclusive') {
                // 外税なら小計に税金を足して、小数点以下切り捨て
                total = Math.floor(subtotal + taxAmount);
            } else if (taxType === 'inclusive') {
                // 内税なら小計そのものが税込合計（念のため切り捨て処理）
                total = Math.floor(subtotal);
            }

            // --- 画面表示 ---
            document.getElementById('subtotal').innerText =
                '¥' +
                subtotal.toLocaleString('ja-JP', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });

            document.getElementById('tax_amount').innerText =
                '¥' +
                taxAmount.toLocaleString('ja-JP', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

            document.getElementById('total_amount').innerText =
                '¥' + total.toLocaleString('ja-JP');
        }

        // --- イベント登録 ---
        document
            .querySelectorAll('.quantity, .unit-price')
            .forEach(function(input) {
                input.addEventListener('input', calculateTotals);
            });

        document
            .getElementById('tax_type_id')
            .addEventListener('change', calculateTotals);

        // --- PDFプレビュー ---
        let previewLoading = false;

        async function previewPdf() {
            if (previewLoading) {
                return;
            }

            previewLoading = true;

            const form = document.getElementById('estimate-form');
            const formData = new FormData(form);

            try {
                const response = await fetch(
                    "{{ route('estimates.preview') }}", {
                        method: 'POST',
                        body: formData
                    }
                );

                // --- エラーチェック ---
                if (!response.ok) {
                    throw new Error('PDF生成に失敗しました');
                }

                // --- Blob化 ---
                const blob = await response.blob();

                // --- URL生成 ---
                const pdfUrl = URL.createObjectURL(blob);

                // --- Fancybox表示 ---
                Fancybox.show([{
                    src: pdfUrl,
                    type: 'iframe'
                }], {
                    on: {
                        destroy: () => {
                            URL.revokeObjectURL(pdfUrl);
                        }
                    }
                });
            } catch (e) {
                console.error(e);
                alert('PDFプレビューの生成に失敗しました');
            } finally {
                previewLoading = false;
            }
        }

        // --- プレビューボタン ---
        $('#preview-btn').on('click', function() {
            previewPdf();
        });

        // --- Sortable 補助CSS ---
        $('<style>')
            .prop('type', 'text/css')
            .html(`
            .ui-state-highlight {
                height: 45px;
                background-color: #fcf8e3;
                border: 1px dashed #fbeed5;
            }
            .item-row.ui-sortable-helper {
                background: #fff;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: table;
                width: 100%;
            }
        `)
            .appendTo('head');

        // --- 初期計算 ---
        calculateTotals();
    });
</script>

@endpush