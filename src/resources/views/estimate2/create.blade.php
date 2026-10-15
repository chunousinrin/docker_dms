@extends('layouts.adminlte')

@section('title', '設計用見積書作成')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">設計用見積書作成</h1>
    <div>
        <a href="{{ route('estimates.index') }}" class="btn btn-sm px-3 btn-secondary">見積書一覧</a>
    </div>
</div>
@stop

@section('content')
<form action="{{ route('estimate2.store') }}" method="POST" id="estimate2Form">
    @csrf
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="user_name" value="{{ $user->name }}">

    {{-- 1. 基本情報 --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" id="headingOne">
            <span><i class="fas fa-file-invoice mr-2"></i>基本情報</span>
            <button type="button" class="btn btn-link ml-auto p-0" data-toggle="collapse" data-target="#collapseOne">
                展開/折り畳み
            </button>
        </div>
        <div id="collapseOne" class="collapse show">
            <div class="card-body">
                <div class="row no-gutters border-bottom border-top">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">見積番号</div>
                    <div class="col-md-10 p-2"><input type="text" class="form-control" name="estimate_number" value="{{ $estimatenum }}" readonly></div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">事業分類 <span class="badge badge-danger ml-auto">必須</span></div>
                    <div class="col-md-10 p-2">
                        <select name="classification_id" class="form-control" required>
                            <option value="" hidden selected>選択してください</option>
                            @foreach($classifications as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- 発行日 + スイッチ (横並び) --}}
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">発行日 <span class="badge badge-danger ml-auto">必須</span></div>
                    <div class="col-md-10 p-2 d-flex align-items-center">
                        <input type="text" name="created_date" class="form-control datepicker col-md-3 mr-3" value="{{ date('Y-m-d') }}">
                        <div class="switch-container border-left pl-3">
                            <span class="switch-label">日付表示</span>
                            <label class="custom-switch mb-0">
                                <input type="hidden" name="cd_display" value="0">
                                <input type="checkbox" name="cd_display" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">発行者</div>
                    <div class="col-md-10 p-2">
                        <select name="branch_id" class="form-control">
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branch->id == 1 ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- 担当者 + スイッチ (横並び) --}}
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">担当者</div>
                    <div class="col-md-10 p-2 d-flex align-items-center">
                        <input type="text" name="user_name" class="form-control col-md-3 mr-3" value="{{ $user->name }}" readonly>
                        <div class="switch-container border-left pl-3">
                            <span class="switch-label">担当表示</span>
                            <label class="custom-switch mb-0">
                                <input type="hidden" name="staff_display" value="0">
                                <input type="checkbox" name="staff_display" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. 取引先・件名情報 --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-building mr-2"></i>取引先・件名情報</span>
            <button type="button" class="btn btn-link ml-auto p-0" data-toggle="collapse" data-target="#collapseTwo">
                展開/折り畳み
            </button>
        </div>
        <div id="collapseTwo" class="collapse show">
            <div class="card-body">
                {{-- 取引先 + 敬称 (横並び) --}}
                <div class="row no-gutters border-bottom border-top">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">取引先 <span class="badge badge-danger ml-auto">必須</span></div>
                    <div class="col-md-10 p-2 d-flex align-items-center">
                        <div class="input-group">

                            <input type="text" name="customer_name" class="form-control col-md-10 rounded-right-0" required>

                            <input type="text" list="honorificOptions" id="honorific" value="様" name="honorific"
                                class="form-control col-md-2 rounded-left-0 rounded-right"
                                onfocus="this.placeholder=this.value; this.value=''"
                                onblur="if(this.value==='') this.value=this.placeholder">

                            <datalist id="honorificOptions">
                                <option value="様">様</option>
                                <option value="御中">御中</option>
                                <option value="殿">殿</option>
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">業務名称 <span class="badge badge-danger ml-auto">必須</span></div>
                    <div class="col-md-10 p-2"><input type="text" name="biz_name" class="form-control" required></div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">業務場所</div>
                    <div class="col-md-10 p-2"><input type="text" name="location" class="form-control"></div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">工種</div>
                    <div class="col-md-10 p-2"><input type="text" name="works_type" class="form-control"></div>
                </div>
                {{-- 業務期間 (横並び) --}}



                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">業務期間</div>
                    <div class="col-md-10 p-2 input-group">
                        <input type="text" name="works_period_start" class="form-control datepicker text-center" placeholder="始期">
                        <div class="input-group-prepend input-group-append"><span class="input-group-text">〜</span></div>
                        <input type="text" name="works_period_end" class="form-control datepicker text-center" placeholder="終期">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. 有効期限・その他 --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clock mr-2"></i>有効期限・その他</span>
            <button type="button" class="btn btn-link ml-auto p-0" data-toggle="collapse" data-target="#collapseThree">
                展開/折り畳み
            </button>
        </div>
        <div id="collapseThree" class="collapse show">
            <div class="card-body">
                <div class="row no-gutters border-bottom border-top">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">見積有効期限</div>
                    <div class="col-md-10 p-2"><input type="text" name="effective_date" class="form-control datepicker" value="" placeholder="未記入の場合、発行日より45日以内"></div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">消費税率</div>
                    <div class="col-md-10 p-2 d-flex align-items-center">
                        <select name="tax_rate" class="form-control col-md-12 mr-2">
                            <option value="1" selected>税抜</option>
                            <option value="1.1">10%</option>
                            <option value="1.08">8%</option>
                        </select>
                    </div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">見積金額</div>
                    <div class="col-md-10 p-2">
                        <select name="total_unit_price" class="form-control col-md-12">
                            <option value="0">表示</option>
                            <option value="1">非表示（単価見積）</option>
                        </select>
                    </div>
                </div>
                <div class="row no-gutters border-bottom">
                    <div class="col-md-2 p-2 d-flex align-items-center label-green">備考</div>
                    <div class="col-md-10 p-2"><textarea name="note" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. 見積明細 (前回提示のものを維持) --}}
    <div class="card mb-3">
        <div class="card-header"><span><i class="fas fa-list mr-2"></i>見積明細</span></div>
        <div class="card-body">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr class="label-green text-center sticky-top" style="border-bottom: 3px solid #70bd29!important;">
                        <th style="width: 40%;">品名・規格・仕様等</th>
                        <th style="width: 10%;">数量</th>
                        <th style="width: 8%;">単位</th>
                        <th style="width: 12%;">単価</th>
                        <th style="width: 15%;">金額</th>
                        <th style="width: 15%;">摘要</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 12; $i++)
                        <tr>
                        <td class="p-2"><input type="text" name="smr{{$i}}" class="form-control "></td>
                        <td class="p-2"><input type="number" step="any" name="qat{{$i}}" class="form-control  text-right calc-input"></td>
                        <td class="p-2"><input type="text" name="unt{{$i}}" class="form-control  text-center"></td>
                        <td class="p-2"><input type="number" step="any" name="up{{$i}}" class="form-control  text-right calc-input"></td>
                        <td class="p-2"><input type="text" name="amt{{$i}}" class="form-control  text-right bg-light amount" readonly tabindex="-1"></td>
                        <td class="p-2"><input type="text" name="rm{{$i}}" class="form-control "></td>
                        </tr>
                        @endfor
                        {{-- 直接作業代(A) --}}
                        <tr class="table-secondary font-weight-bold">
                            <td colspan="4" class="text-right align-middle">直接作業代 (A)</td>
                            <td><input type="text" id="subtotal" name="subtotal" class="form-control text-right bg-transparent font-weight-bold" readonly tabindex="-1"></td>
                            <td class="text-center small align-middle text-muted">小計</td>
                        </tr>

                        {{-- 諸経費1〜2 (構造を他と統一) --}}
                        @foreach([
                        ['L'=>'', 'R'=>'idqat1', 'A'=>'idamt1', 'F'=>'A×B', 'N'=>'idsmr1', 'U'=>'idunt1', 'P'=>'idup1','V'=>'readonly','Y'=>'%', 'X'=>'自動入力'],
                        ['L'=>'', 'R'=>'idqat2', 'A'=>'idamt2', 'F'=>'(A+C)×D', 'N'=>'idsmr2', 'U'=>'idunt2', 'P'=>'idup2','V'=>'','Y'=>'', 'X'=>'']
                        ] as $e)
                        <tr>
                            {{-- 項目名 --}}
                            <td class="p-2">
                                <input type="text" name="{{$e['N']}}" class="form-control" value="{{$e['L']}}">
                            </td>
                            {{-- 数量（または率） --}}
                            <td class="p-2">
                                <input type="number" step="0.01" id="{{$e['R']}}" name="{{$e['R']}}" class="form-control text-right calc-input">
                            </td>
                            {{-- 単位 --}}
                            <td class="p-2">
                                <input type="text" name="{{$e['U']}}" class="form-control text-center" value="{{$e['Y']}}" {{ $e['V'] }}>
                            </td>
                            {{-- 単価 --}}
                            <td class="p-2">
                                <input type="number" step="any" id="{{$e['P']}}" name="{{$e['P']}}" class="form-control text-right calc-input" readonly>
                            </td>
                            {{-- 金額 --}}
                            <td class="p-2">
                                <input type="text" id="{{$e['A']}}" name="{{$e['A']}}" class="form-control text-right calc-input" placeholder="{{$e['X']}}">
                            </td>
                            {{-- 計算根拠 (摘要) --}}
                            <td class=" text-center small align-middle text-muted">{{$e['F']}}
                            </td>
                        </tr>
                        @endforeach

                        {{-- 工事原価(F) --}}
                        <tr class="table-secondary font-weight-bold">
                            <td colspan="4" class="text-right align-middle">工事原価 (F)</td>
                            <td><input type="text" id="idtotal" name="idtotal" class="form-control text-right bg-transparent font-weight-bold" readonly tabindex="-1"></td>
                            <td class="text-center small align-middle text-muted">A+C+E</td>
                        </tr>
                        {{-- 諸経費3 (構造を他と統一) --}}
                        <tr>
                            {{-- 項目名 --}}
                            <td class="p-2">
                                <input type="text" name="otsmr1" class="form-control">
                            </td>
                            {{-- 数量（率%） --}}
                            <td class="p-2">
                                <input type="number" step="0.01" id="otqat1" name="otqat1" class="form-control text-right calc-input">
                            </td>
                            {{-- 単位 --}}
                            <td class="p-2">
                                <input type="text" name="otunt1" class="form-control text-center" value="%" readonly>
                            </td>
                            {{-- 単価 --}}
                            <td class="p-2">
                                <input type="number" step="any" id="otup1" name="otup1" class="form-control text-right calc-input" readonly>
                            </td>
                            {{-- 金額 --}}
                            <td class="p-2">
                                <input type="text" id="otamt1" name="otamt1" class="form-control text-right calc-input" placeholder="自動入力">
                            </td>
                            {{-- 計算根拠 (摘要) --}}
                            <td class="text-center small align-middle text-muted">F×G</td>
                        </tr>
                        {{-- その他経費 I,J,K --}}
                        @foreach(['I'=>2,'J'=>3,'K'=>4] as $l=>$idx)
                        <tr>
                            <td class="p-2"><input type="text" name="otsmr{{$idx}}" class="form-control"></td>
                            <td class="p-2"><input type="number" step="any" id="otqat{{$idx}}" name="otqat{{$idx}}" class="form-control text-right calc-input"></td>
                            <td class="p-2"><input type="text" name="otunt{{$idx}}" class="form-control text-center"></td>
                            <td class="p-2"><input type="number" step="any" id="otup{{$idx}}" name="otup{{$idx}}" class="form-control text-right calc-input" readonly></td>
                            <td class="p-2"><input type="text" id="otamt{{$idx}}" name="otamt{{$idx}}" class="form-control text-right calc-input"></td>
                            <td class="text-center small align-middle text-muted">{{$l}}</td>
                        </tr>
                        @endforeach
                        {{-- 最終合計 --}}
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="4" class="text-right align-middle" style="font-size: 1.1rem;">最終合計 (L)</td>
                            <td><input type="text" id="ottotal" name="ottotal" class="form-control text-right bg-transparent text-white font-weight-bold" readonly tabindex="-1" style="font-size: 1.1rem;"></td>
                            <td class="text-center small align-middle">税込</td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 text-center">
        <button type="button" id="btn-preview-pdf" class="btn btn-sm btn-secondary px-4 mr-2">
            <i class="far fa-file-pdf"></i> プレビュー
        </button>
        <button type="button" id="btn-save-estimate" class="btn btn-sm btn-primary px-5">
            <i class="fas fa-save"></i> 保存する
        </button>
    </div>
</form>

@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<style>
    .label-green {
        background-color: var(--dms-light-green);
    }

    .switch-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .custom-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .switch-label {
        font-size: 0.85rem;
        font-weight: bold;
        color: #495057;
        min-width: 55px;
    }

    .fancybox__content {
        background: none !important;
        overflow: hidden;
    }

    .fancybox__html-contents {
        padding: 0 !important;
    }

    /* 閉じるボタンの位置調整（枠を消した分、少し内側に入れるときれいです） */
    .f-button.is-close-btn {
        top: 15px !important;
        right: 15px !important;
        background: rgba(0, 0, 0, 0.5) !important;
        /* 半透明の黒にするとPDFと被っても見やすい */
        color: #fff !important;
        border: 3px solid white !important;
        border-radius: 50%;
    }
</style>
@endsection

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<script>
    $(function() {
        // 1. デイトピッカーの初期化
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            showButtonPanel: true,
        });

        // 2. 計算ロジックのイベント設定
        $(document).on('input', '.calc-input, select[name="tax_rate"]', calculateEstimate);

        function calculateEstimate() {
            const getNum = (id) => parseFloat($(`#${id}`).val()?.replace(/,/g, '')) || 0;
            const setFmt = (id, val) => $(`#${id}`).val(val !== 0 ? Math.floor(val).toLocaleString() : '');

            let subA = 0;
            for (let i = 1; i <= 12; i++) {
                const q = parseFloat($(`input[name="qat${i}"]`).val()) || 0;
                const u = parseFloat($(`input[name="up${i}"]`).val()) || 0;
                const rowAmt = Math.floor(q * u);
                if (q !== 0 || u !== 0) {
                    $(`input[name="amt${i}"]`).val(rowAmt.toLocaleString());
                    subA += rowAmt;
                } else {
                    $(`input[name="amt${i}"]`).val('');
                }
            }
            setFmt('subtotal', subA);

            const r1 = parseFloat($('#idqat1').val()) || 0;
            let c = r1 !== 0 ? Math.floor((subA * (r1 / 100)) / 1000) * 1000 : getNum('idamt1');
            setFmt('idamt1', c);

            const r2 = parseFloat($('#idqat2').val()) || 0;
            let e = r2 !== 0 ? Math.floor(((subA + c) * (r2 / 100)) / 1000) * 1000 : getNum('idamt2');
            setFmt('idamt2', e);

            const totalF = subA + c + e;
            setFmt('idtotal', totalF);

            const r3 = parseFloat($('#otqat1').val()) || 0;
            let h = r3 !== 0 ? Math.floor((totalF * (r3 / 100)) / 1000) * 1000 : getNum('otamt1');
            setFmt('otamt1', h);

            let otherSum = 0;
            [2, 3, 4].forEach(idx => {
                const q = parseFloat($(`#otqat${idx}`).val()) || 0;
                const u = parseFloat($(`#otup${idx}`).val()) || 0;
                let amt = (q !== 0 && u !== 0) ? Math.floor(q * u) : getNum(`otamt${idx}`);
                setFmt(`otamt${idx}`, amt);
                otherSum += amt;
            });

            // 税率取得（selectタグに合わせて修正）
            const taxRateVal = parseFloat($('select[name="tax_rate"]').val()) || 1;
            const finalTotal = Math.floor((totalF + h + otherSum) * taxRateVal);
            setFmt('ottotal', finalTotal);
        }

        // 3. Fancyboxを使ったPDFプレビュー処理
        $('#btn-preview-pdf').on('click', function(e) {
            e.preventDefault();

            // 一意のターゲット名を作成
            const targetName = 'fancybox_pdf_target';

            // 1. 先にFancyboxを立ち上げ、その中に<iframe>を生成する
            Fancybox.show([{
                // HTMLとして<iframe>を埋め込む
                src: `<iframe name="${targetName}" id="fancybox-iframe" src="about:blank" style="width:80vw; height:80vh; border:none;"></iframe>`,
                type: "html",
            }], {
                width: '95%',
                height: '95%',
                dragToClose: false,
                Toolbar: {
                    display: {
                        right: ["close"],
                    },
                },
            });

            // 2. プレビュー用の一時的な隠しフォームを作成
            const $tempForm = $('<form>', {
                method: 'POST',
                action: "{{ route('estimate2.preview') }}",
                target: targetName // 上記<iframe>のname属性と一致させる
            }).append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));

            // 3. 現在のフォームの値をコピー（カンマ除去も継続）
            $('#estimate2Form').find(':input').each(function() {
                if (!this.name || this.name === '_token') return;

                val = this.value;

                // ★チェックボックス特有の処理を追加
                if (this.type === 'checkbox') {
                    if (!this.checked) {
                        // チェックがない場合はこのループをスキップ（hidden側の0に任せる）
                        return;
                    }
                }

                if (val && typeof val === 'string') {
                    // 全角スペースを一時的な文字列に置換して、トリミングを逃れる
                    val = val.replace(/　/g, '###ZS###');
                }
                // ★追加：カンマが含まれる数値項目のカンマを除去して送る
                if (this.name.match(/^(subtotal|idtotal|ottotal|amt|idamt|otamt|up|qat|idup|otup|total_unit_price)/)) {
                    val = val.replace(/,/g, '');
                }

                $tempForm.append($('<input>', {
                    type: 'hidden',
                    name: this.name,
                    value: val
                }));
            });

            // 4. 送信！ (bodyに追加して送信直後に削除)
            // 少しだけ遅延させることで、ブラウザがiframeを認識しやすくなります
            setTimeout(() => {
                $('body').append($tempForm);
                $tempForm.submit();
                $tempForm.remove();
            }, 50);
        });

        $('#btn-save-estimate').on('click', function() {
            const $form = $('#estimate2Form');

            const $tempForm = $('<form>', {
                method: 'POST',
                action: $form.attr('action')
            }).append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));

            $form.find(':input').each(function() {
                if (!this.name || this.name === '_token') return;

                // checkbox の OFF は hidden の 0 に任せる
                if (this.type === 'checkbox' && !this.checked) return;

                let val = this.value ?? '';

                // 全角スペース保護
                if (typeof val === 'string') {
                    val = val.replace(/　/g, '###ZS###');
                }

                // prepareData() の numericFields と完全一致させる
                if (this.name.match(/^(subtotal|idtotal|ottotal|idamt|otamt|idup|otup|idqat|otqat|total_unit_price|qat|up|amt|tax_rate|smr)/)) {
                    val = val.replace(/,/g, '');
                }

                $tempForm.append($('<input>', {
                    type: 'hidden',
                    name: this.name,
                    value: val
                }));
            });

            $('body').append($tempForm);
            $tempForm.submit();
        });

        // 初回計算
        calculateEstimate();
    });
</script>
@endsection