<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>請求書</title>
    <style>
        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Italic.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-BoldItalic.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        @page {
            size: A4;
        }

        * {
            font-family: 'NotoSansJP';
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }

        body {
            padding: 15mm;
            margin-bottom: 8mm;
            font-size: 10pt;
        }

        .insho {
            position: absolute;
            top: 0;
            right: 2mm;
            width: 19mm;
            opacity: 0.65;
        }

        .tbl {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            /* セル間に隙間を作ります */
            border-spacing: 0;
            /* 隙間を0にして隣接させます */
            table-layout: fixed;
        }

        .tbl tr th,
        .tbl tr td {
            border: 1px solid silver;
            padding: 1.7px 10px;
        }

        .tbl thead tr th,
        .tbl tfoot tr th {
            font-weight: 400;
            background-color: rgba(112, 189, 41, 0.5) !important;
        }

        .summary tr td {
            padding: 0 1% !important;
            height: 23pt;
            line-height: 8pt;
            word-break: break-all;
            /* 枠線に合わせてどこでも強制的に折り返す */
            overflow-wrap: break-word;
            /* 英語の単語単位などでも溢れないように安全弁として */

        }


        .tbl tr td:nth-of-type(n+2):nth-of-type(-n+4) {
            text-align: center;
        }

        .tbl tr td:last-child {
            text-align: right;
        }

        .banklist {
            border-collapse: collapse;
        }

        .banklist td {
            padding: 0;
            margin: 0;
            border: none !important;
            line-height: 8pt;
            text-align: left !important;
        }
    </style>
</head>

<body>
    <main>
        <table style="width: 100%;margin-bottom: 5mm;">
            <tr>
                <td style="width: 50%;vertical-align: top;">
                    <h1 style="font-size: 13pt;font-weight:400;text-align:center;padding:0;border-bottom:3px double black;margin-bottom:5mm;height:27px;line-height:25px;">
                        <span style="display: inline-block;width:30px;">御</span>
                        <span style="display: inline-block;width:30px;">請</span>
                        <span style="display: inline-block;width:30px;">求</span>
                        <span style="display: inline-block;width:30px;">書</span>
                    </h1>
                    <div style="font-size: 13pt;border-bottom:1px solid black;padding-left: 2mm;line-height:8pt;">{{ $invoice['customer_name'] }}&#x3000;{{ $invoice['honorific']}}</div>
                    <div style="margin-bottom: 5mm;padding-left: 2mm;">下記のとおり、ご請求申し上げます。</div>
                    <div style="border-bottom: 1px solid silver;margin-bottom: 2mm;line-height:10pt;">
                        <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">
                            @if($invoice['invoice_category_id']==15)
                            件名
                            @else
                            事業名
                            @endif
                        </span>
                        {{ $invoice['project_name'] }}
                    </div>
                    <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                        <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">
                            @if($invoice->invoice_category_id==15)
                            納入場所
                            @else
                            場所
                            @endif
                        </span>
                        {{ $invoice['project_location'] }}
                    </div>
                    @if($invoice['invoice_category_id']!=15)
                    <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                        <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">実施日</span>
                        {{ to_wareki($invoice['completion_start_date']) }}
                        @if(!empty($invoice['completion_end_date']))
                        ～
                        {{ to_wareki($invoice['completion_end_date']) }}
                        @endif
                    </div>
                    @else
                    <div style="line-height:7pt;margin-bottom: 2mm;">&nbsp;</div>
                    @endif

                    <div style="font-size: 13pt;font-weight:400;border-bottom:2px solid black;padding-left: 2mm;line-height:13pt;">
                        <span style=" display:inline-block;width:25mm;font-size:11pt;vertical-align:bottom;padding-bottom:2px;">合計金額</span>
                        ￥ {{ number_format($invoice['total_billing'] ?? 0) }} -
                    </div>

                    <div style="padding-left: 2mm;line-height:10pt">お支払期日：
                        @if(empty($invoice['due_date']))
                        ご請求日より45日以内
                        @else
                        {{ to_wareki($invoice['due_date']) }}
                        @endif
                    </div>

                </td>
                <td style="width: 10%;"></td>
                <td style="width: 40%;vertical-align: top;">
                    <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                        <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">請求番号</span>
                        {{ $invoice->invoice_number }}
                    </div>
                    <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 5mm;">
                        <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px;">発行日</span>
                        @if($invoice->show_billing_date== 1 )
                        {{ to_wareki($invoice['billing_date']) }}
                        @endif
                    </div>
                    <div style="position:relative;">
                        <h4 style="font-size: 13pt;margin-bottom:2mm;line-height:10pt;font-weight:400"> {{ $requester->name ?? '会社名未設定' }} <br>{{ $requester->representative ?? '' }}
                        </h4>
                        @if(!empty($stampBase64))
                        <img class="insho" src="data:image/png;base64,{{ $stampBase64 }}" alt="印章">
                        @endif
                        <div style="margin-bottom: 2mm;">登録番号：T4200005007974</div>
                        <div style="line-height: 9pt;">〒{{ $requester->postal_code ?? '501-3782' }}</div>
                        <div style="line-height: 9pt;">{{ $requester->address ?? '岐阜県美濃市長瀬427番8' }}</div>
                        <div style="line-height: 9pt;">Tel.{{$requester->phone ?? '0575-35-3010' }}</div>
                        <div style="line-height: 9pt;">Fax.{{ $requester->fax ?? '0575-31-0388' }}</div>
                        @if($invoice->show_creator== 1 )
                        担当者：{{ $creator->name }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="tbl summary">
            <thead>
                <tr>
                    <th style=" width: 56%;">
                        @if($invoice->invoice_category_id == 15)
                        品名
                        @else
                        摘要
                        @endif
                    </th>
                    <th style="width: 11%;text-align:center;">数量</th>
                    <th style="width: 11%;text-align:center;">単位</th>
                    <th style="width: 11%;text-align:center;">単価</th>
                    <th style="width: 11%;;">金額</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1から12まで確実に回す --}}
                @for ($i = 1; $i <= 15; $i++)
                    @php
                    // displayItemsの中から、現在の項番 ($i) に一致するデータを探す
                    $item=$displayItems->firstWhere('order_number', $i);

                    // データが存在し、かつ内容があるか判定
                    $hasData = $item && (!empty($item->description) || !empty($item->quantity) || !empty($item->unit_price));
                    @endphp
                    <tr>
                        {{-- データがある場合のみ中身を表示 --}}
                        <td class="d-flex" style="padding-left:0.5em!important;">
                            {{ $hasData ? $item->description : '' }}<span style="opacity: 0;font-size:1px;">{{ $i }}</span></pre>
                        </td>
                        <td class="text-right">{{ $hasData ? $item->quantity : '' }}</td>
                        <td>{{ $hasData ? $item->unit : '' }}</td>
                        <td class="text-right">
                            {{ ($hasData && $item->unit_price) ? number_format($item->unit_price) : '' }}
                        </td>
                        <td class="text-right" style="padding-right: 0.5em!important;">
                            {{ ($hasData && ($item->quantity * $item->unit_price)) ? number_format($item->quantity * $item->unit_price) : '' }}
                        </td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" rowspan="3" style="border:none;vertical-align:top;">
                        <div style="height:15px;line-height:1;vertical-align:top;">備考：</div>
                        <div style="margin-top:0.8em;line-height:0.8;">{{ $invoice->remarks ?? '' }}</div>
                    </td>
                    <th>10%対象</th>
                    <td style="text-align: right;padding-right: 0.5em!important;">
                        @if($invoice->taxType->rate==10)
                        @switch($invoice->taxType->type)
                        @case('exclusive')
                        {{ number_format($invoice['total_billing'] - $invoice['tax_amount']) }}
                        @break
                        @case('inclusive')
                        {{ number_format($invoice['total_billing']) }}
                        @break
                        @case('none')
                        {{-- 非表示 --}}
                        @break
                        @endswitch
                        @endif
                    </td>
                    <td style="font-size: 8pt;position:relative;text-align:right;padding-right: 0.5em!important;">
                        <span style="font-size:5pt;position: absolute;top:0;left:5px;">
                            @switch($invoice->taxType->type)
                            @case('exclusive')
                            消費税
                            @break
                            @case('inclusive')
                            内消費税
                            @break
                            @case('none')
                            {{-- 非表示 --}}
                            @break
                            @endswitch
                        </span>
                        @if($invoice->taxType->rate==10)
                        {{ !empty($invoice->tax_amount) ? number_format($invoice->tax_amount) : '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>8%対象</th>
                    <td style="text-align: right;padding-right: 0.5em!important;">
                        @if($invoice->taxType->rate==8)
                        {{ !empty($invoice->total_billing) ? number_format($invoice->total_billing) : '' }}
                        @endif
                    </td>
                    <td style="font-size: 8pt;position:relative;text-align:right;padding-right: 0.5em!important;">
                        <span style="font-size:5pt;position: absolute;top:0;left:5px;">
                            @switch($invoice->taxType->type)
                            @case('exclusive')
                            消費税
                            @break
                            @case('inclusive')
                            内消費税
                            @break
                            @case('none')
                            {{-- 非表示 --}}
                            @break
                            @endswitch
                        </span>
                        @if($invoice->taxType->rate==8)
                        {{ !empty($invoice->tax_amount) ? number_format($invoice->tax_amount) : '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>合計</th>
                    <td style="text-align: right;padding-right: 0.5em!important;">
                        @switch($invoice->taxType->type)
                        @case('exclusive')
                        {{ number_format($invoice['total_billing'] - $invoice['tax_amount']) }}
                        @break
                        @case('inclusive')
                        {{ number_format($invoice['total_billing']) }}
                        @break
                        @case('none')
                        {{-- 非表示 --}}
                        @break
                        @endswitch
                    </td>
                    <td style="font-size: 8pt;position:relative;text-align:right;padding-right: 0.5em!important;">
                        <span style="font-size:5pt;position: absolute;top:0;left:5px;">
                            @switch($invoice->taxType->type)
                            @case('exclusive')
                            消費税
                            @break
                            @case('inclusive')
                            内消費税
                            @break
                            @case('none')
                            {{-- 非表示 --}}
                            @break
                            @endswitch
                        </span>
                        {{ !empty($invoice['tax_amount']) ? number_format($invoice['tax_amount']) : '' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </main>
    <footer style="width:180mm;position:absolute;bottom:15mm;left:15mm;">
        <table class="tbl">
            <tr>
                <td style="padding-left:2mm;width:15%;background-color:rgba(112, 189, 41, 0.5)">振込口座</td>
                <td style="width: 67%;border-right:none;">
                    <table class="banklist">
                        @foreach($banks as $bank)
                        @if($bank)
                        <tr>
                            <td>{{ $bank->bank_name }} </td>
                            <td>{{ $bank->branch_name }}</td>
                            <td>{{ $bank->account_type }}</td>
                            <td>{{ $bank->account_number }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </table>
                </td>
                <td style="width: 18%;border-left:none;text-align:center;">
                    <img src="{{ asset('storage/img/logo.svg') }}" alt="ロゴ" style="width:100%;height:auto;">
                    <div style="width:100%;background-color:#70bd29;color:white;font-size:6pt;height:12px!important;line-height:7px;padding:0;margin-top:5px;">
                        {{ $invoice['requester_name'] ?? '中濃森林組合' }}
                    </div>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>