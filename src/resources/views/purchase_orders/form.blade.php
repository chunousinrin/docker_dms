@php
$items = isset($purchaseOrder)
? $purchaseOrder->items->keyBy('order_number')
: collect();
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">発注先情報</h3>
            </div>
            <div class="card-body">
                {{-- 左カラム --}}
                <div class="row">
                    {{-- 発注番号 --}}
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold text-dark mb-1 small">発注番号</label>
                        <input type="text" class="form-control" value="{{ old('order_number', $purchaseOrder->order_number ?? $tempOrderNumber) }}" readonly>
                        <input type="hidden" name="order_number" value="{{ old('order_number', $purchaseOrder->order_number ?? $tempOrderNumber) }}">
                    </div>

                    {{-- 発注日 --}}
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold text-dark mb-1 small">発注日</label>
                        <input type="text" name="order_date" class="form-control @error('order_date') is-invalid @enderror datepicker" value="{{ old('order_date', optional($purchaseOrder->order_date ?? now())->format('Y-m-d')) }}">
                        @error('order_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    {{-- 発注先名 --}}
                    <div class="form-group col-md-10">
                        <label class="font-weight-bold text-dark mb-1 small">発注先名</label>
                        <input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name', $purchaseOrder->supplier_name ?? '') }}">
                        @error('supplier_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 敬称 --}}
                    <div class="form-group col-md-2">
                        <label class="font-weight-bold text-dark mb-1 small">敬称</label>
                        <select name="supplier_title" class="form-control select2">
                            @foreach(['御中', '様'] as $title)
                            <option value="{{ $title }}" @selected(old('supplier_title', $purchaseOrder->supplier_title ?? '御中') == $title)>
                                {{ $title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    {{-- 住所 --}}
                    <div class="form-group col-md-12">
                        <label class="font-weight-bold text-dark mb-1 small">住所</label>
                        <input type="text" name="supplier_address" class="form-control" value="{{ old('supplier_address', $purchaseOrder->supplier_address ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    {{-- 部署 --}}
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold text-dark mb-1 small">部署</label>
                        <input type="text" name="supplier_department" class="form-control" value="{{ old('supplier_department', $purchaseOrder->supplier_department ?? '') }}">
                    </div>

                    {{-- 担当者 --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold text-dark mb-1 small">担当者</label>
                        <input type="text" name="supplier_person" class="form-control" value="{{ old('supplier_person', $purchaseOrder->supplier_person ?? '') }}">
                    </div>

                    {{-- 敬称 --}}
                    <div class="form-group col-md-2">
                        <label class="font-weight-bold text-dark mb-1 small">敬称</label>
                        <select name="supplier_person_title" class="form-control select2">
                            <option value="">なし</option>
                            @foreach(['様', '殿'] as $title)
                            <option value="{{ $title }}" @selected(old('supplier_person_title', $purchaseOrder->supplier_person_title ?? '様') == $title)>
                                {{ $title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>


        {{-- 発注明細カード --}}
        <div class="card mt-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th style="width: 0; white-space: nowrap;" class="text-center align-middle p-2"><i class="fas fa-sort"></i></th>
                                <th style="width: 0; white-space: nowrap;">#</th>
                                <th>品名 / 項目</th>
                                <th style="width:12%;">数量</th>
                                <th style="width:12%;">単位</th>
                                <th style="width:12%;">単価</th>
                                <th style="width:12%;">金額</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-items">
                            @for($i = 1; $i <= 15; $i++)

                                @php
                                $item=$items->get($i);
                                @endphp
                                <tr class="item-row">
                                    <td class="text-center align-middle p-2" style="cursor: grab;">
                                        <i class="fas fa-bars"></i>
                                    </td>

                                    <td class="text-center align-middle row-number p-0">{{ $i }}</td>
                                    <td>
                                        <input type="text" name="items[{{ $i }}][item_name]" class="form-control form-control-sm item-name" value="{{ old("items.$i.item_name", $item?->item_name) }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ old("items.$i.quantity", $item?->quantity) }}">
                                    </td>
                                    <td>
                                        <div class="input-group-sm">
                                            <select name="items[{{ $i }}][unit]" class="select2 text-center unit">
                                                <option value=""></option>
                                                @php
                                                $units = ['式', '本', '台', '個', '枚', '袋', '箱', '時間', '日', 'm', '㎡', '㎥', 'kg', 'L'];
                                                $selectedValue = old("items.$i.unit", $item?->unit);
                                                @endphp
                                                @foreach ($units as $unit)
                                                <option value="{{ $unit }}" {{ $selectedValue == $unit ? 'selected' : '' }}>
                                                    {{ $unit }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm text-right unit-price" value="{{ old("items.$i.unit_price", $item?->unit_price) }}">
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $i }}][amount]" class="form-control form-control-sm text-right amount bg-light" value="{{ old("items.$i.amount", $item?->amount) }}" readonly>
                                    </td>
                                    <input type="hidden" name="items[{{$i}}][order_number]" class="item-order" value="{{$i}}">

                                </tr>
                                @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- 右カラム --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">発注元情報</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- 発行者 --}}
                    <div class="form-group col-md-12">
                        <label class="font-weight-bold text-dark mb-1 small">発行者</label>
                        <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $purchaseOrder->branch_id ?? auth()->user()->branch_id ?? 1) == $branch->id)>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 担当者 --}}
                    <div class="form-group col-md-12">
                        <label class="font-weight-bold text-dark mb-1 small">担当者</label>
                        <select name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            @foreach($users->where('used', 1)->sortBy('id') as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id', $purchaseOrder->user_id ?? auth()->id()) == $user->id)>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 税率 --}}
                    <div class="form-group col-md-12">
                        <label class="font-weight-bold text-dark mb-1 small">税率</label>
                        <select name="tax_type_id" id="tax_type_id" class="form-control @error('tax_type_id') is-invalid @enderror">
                            @foreach($taxTypes as $taxType)
                            <option
                                value="{{ $taxType->id }}"
                                data-rate="{{ $taxType->rate }}"
                                data-type="{{ $taxType->type }}"
                                @selected(old('tax_type_id', $purchaseOrder->tax_type_id ?? 1) == $taxType->id)>
                                {{ $taxType->name }}
                            </option>
                            @endforeach
                        </select> @error('tax_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    {{-- 備考 --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">備考</label>
                            <textarea name="remarks" rows="5" class="form-control">{{ old('remarks', $purchaseOrder->remarks ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- 計算結果テーブル --}}
                    <div class="col-md-12">

                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between">
                                <span>小計:</span>
                                <span id="subtotal">¥{{ number_format(old('subtotal', $purchaseOrder->subtotal ?? 0)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>消費税:</span>
                                <span id="tax_amount">¥{{ number_format(old('tax_amount', $purchaseOrder->tax_amount ?? 0)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between font-weight-bold">
                                <span>税込合計:</span>
                                <span id="total_amount" class="text-primary">¥{{ number_format(old('total_amount', $purchaseOrder->total_amount ?? 0)) }}</span>
                            </div>
                        </div>
                        <input type="hidden" id="subtotal2" name="subtotal" value="{{ old('subtotal', $purchaseOrder->subtotal ?? 0) }}">
                        <input type="hidden" id="tax_amount2" name="tax_amount" value="{{ old('tax_amount', $purchaseOrder->tax_amount ?? 0) }}">
                        <input type="hidden" id="total_amount2" name="total_amount" value="{{ old('total_amount', $purchaseOrder->total_amount ?? 0) }}">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <!-- 左側：全体の70%くらいの幅を持たせる -->
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save mr-1"></i> 保存
                        </button>
                    </div>
                    <!-- 右側：残りのスペースにボタンを寄せる -->
                    <div class="col-md-5 text-right text-end">
                        <button type="button" class="btn btn-sm btn-info" id="previewPdf" title="PDF">
                            <i class="far fa-file-pdf"></i>
                        </button>
                        <button type="reset" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="リセット">
                            <i class="fas fa-undo"></i>
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="キャンセル">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- 備考・合計金額計算カード --}}

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initializeEvents();
        initializeRows();
        calculateTotals();
    });

    /**
     * 数値変換
     */
    function number(value) {
        const n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    /**
     * 初期化
     */
    function initializeRows() {
        document.querySelectorAll("tbody tr").forEach(row => calculateRow(row));
    }

    /**
     * イベント登録
     */
    function initializeEvents() {
        // 数量・単価変更
        document.querySelectorAll(".quantity, .unit-price").forEach(input => {
            input.addEventListener("input", event => {
                const row = event.target.closest("tr");
                calculateRow(row);
                calculateTotals();
            });
        });

        // 税率変更
        document.getElementById("tax_type_id")?.addEventListener("change", () => {
            calculateTotals();
        });

        // Enterキー移動
        initializeEnterKey();
    }

    /**
     * 1行計算
     */
    function calculateRow(row) {

        const quantity = number(
            row.querySelector(".quantity").value
        );

        const unitPrice = number(
            row.querySelector(".unit-price").value
        );

        const amount = Math.floor(quantity * unitPrice);

        row.querySelector(".amount").value =
            amount || amount === 0 ? amount : "";

    }

    /**
     * 合計計算
     */
    function formatCurrency(amount) {
        return '¥' + new Intl.NumberFormat('ja-JP').format(Math.floor(amount));
    }

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll(".amount").forEach(input => {
            subtotal += number(input.value);
        });

        document.getElementById("subtotal").textContent = formatCurrency(subtotal);
        document.getElementById("subtotal2").value = subtotal;

        calculateTax(subtotal);
    }

    /**
     * 消費税計算
     */
    function calculateTax(subtotal) {
        if (subtotal === undefined) {
            subtotal = number(document.getElementById("subtotal").textContent);
        }

        const select = document.getElementById("tax_type_id");

        if (!select || select.selectedIndex < 0) {
            document.getElementById("tax_amount").textContent = formatCurrency(0);
            document.getElementById("total_amount").textContent = formatCurrency(subtotal);
            return;
        }

        const option = select.selectedOptions[0];
        const rate = number(option.dataset.rate);
        const type = option.dataset.type;

        let tax = 0;
        let total = subtotal;

        switch (type) {
            case "exclusive":
                tax = subtotal * rate / 100;
                total = subtotal + tax;
                break;
            case "inclusive":
                tax = subtotal * rate / (100 + rate);
                total = subtotal;
                break;
        }

        tax = Math.floor(tax);
        total = Math.floor(total);

        document.getElementById("tax_amount").textContent = formatCurrency(tax);
        document.getElementById("tax_amount2").value = tax;
        document.getElementById("total_amount").textContent = formatCurrency(total);
        document.getElementById("total_amount2").value = total;
    }

    // --- Sortable 行番号更新 ---
    window.updateRowNumbers = function() {

        $('#sortable-items tr').each(function(index) {
            const no = index + 1;
            // 表示番号
            $(this).find('.row-number').text(no);
            // hidden
            $(this).find('.item-order').val(no).attr('name', `items[${no}][order_number]`);
            // 品名
            $(this).find('.item-name').attr('name', `items[${no}][item_name]`);
            // 数量
            $(this).find('.quantity').attr('name', `items[${no}][quantity]`);
            // 単位
            $(this).find('[name$="[unit]"]').attr('name', `items[${no}][unit]`);
            // 単価
            $(this).find('.unit-price').attr('name', `items[${no}][unit_price]`);
            // 金額
            $(this).find('.amount').attr('name', `items[${no}][amount]`);
        });
    };

    /**
     * Enterキー移動
     */
    function initializeEnterKey() {
        const selectors = [
            ".item-name",
            ".quantity",
            ".unit",
            ".unit-price",
            ".remarks"
        ];

        document.addEventListener("keydown", function(e) {
            if (e.key !== "Enter") {
                return;
            }

            const target = e.target;
            const selector = selectors.find(s => target.matches(s));

            if (!selector) {
                return;
            }

            e.preventDefault();

            const currentIndex = selectors.indexOf(selector);
            const row = target.closest("tr");

            // 同じ行の次の入力欄
            if (currentIndex < selectors.length - 1) {
                const next = row.querySelector(selectors[currentIndex + 1]);
                if (next) {
                    next.focus();
                    next.select?.();
                }
                return;
            }

            // 次の行の先頭
            const nextRow = row.nextElementSibling;
            if (!nextRow) {
                return;
            }

            const next = nextRow.querySelector(selectors[0]);
            if (next) {
                next.focus();
                next.select?.();
            }
        });
    }
    document.getElementById('previewPdf').addEventListener('click', async function() {
        const form = this.closest('form');
        const formData = new FormData(form);
        formData.delete('_method');

        const response = await fetch(
            "{{ route('purchase-orders.pdf-preview') }}", {
                method: "POST",
                body: formData,
            }
        );
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);

        Fancybox.show([{
            src: url,
            type: "iframe"
        }]);
    });
</script>
@endsection