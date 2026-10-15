<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <style>
        /* 日本語フォントの設定（ipaexgなどをインストールしている場合） */
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

        body {
            font-family: 'NotoSansJP';
            font-size: 10pt;
            color: #333;
        }

        .title {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .tbl {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            /* セル間に隙間を作ります */
            border-spacing: 0;
            /* 隙間を0にして隣接させます */
        }

        .tbl tr th,
        .tbl tr td {
            border: 1px solid silver;
            padding: 1.7px 10px;
            height: 23pt;
        }

        .tbl thead tr th,
        .tbl tfoot tr th {
            font-weight: 400;
            background-color: rgba(112, 189, 41, 0.5) !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .insho {
            position: absolute;
            top: 2mm;
            right: 2mm;
            width: 19mm;
            opacity: 0.65;
        }
    </style>
</head>

<body>
    @php
    $showPrice = $estimate['unit_price_estimate'] != 0;
    @endphp
    <main>
        <table style="width: 100%; margin-bottom: 5mm;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h1 style="font-size: 13pt; font-weight: 400; text-align: center; padding: 0; border-bottom: 3px double black; margin-bottom: 5mm; height: 27px; line-height: 25px;">
                        <span style="display: inline-block; width: 30px;">御</span>
                        <span style="display: inline-block; width: 30px;">見</span>
                        <span style="display: inline-block; width: 30px;">積</span>
                        <span style="display: inline-block; width: 30px;">書</span>
                    </h1>
                    <div style="font-size: 13pt; border-bottom: 1px solid black; padding-left: 2mm; line-height: 8pt;">{{ $estimate['customer_name'] }}&#x3000;{{ $estimate['honorific'] }}</div>
                    <div style="margin-bottom: 5mm; padding-left: 2mm;">下記のとおり、御見積申し上げます。</div>
                    <div style="border-bottom: 1px solid silver; margin-bottom: 2mm; line-height: 10pt;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">事業名</span>
                        {{ $estimate['project_name'] }}
                    </div>
                    <div style="border-bottom: 1px solid silver; line-height: 7pt; margin-bottom: 2mm;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">場所</span>
                        {{ $estimate['project_location'] }}
                    </div>
                    <div style="border-bottom: 1px solid silver; line-height: 7pt; margin-bottom: 2mm;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">実施予定日</span>
                        @if(!empty($estimate['scheduled_date']))
                        {{ to_wareki($estimate['scheduled_date']) }}
                        @else
                        未定
                        @endif
                    </div>

                    <div style="border-bottom: 1px solid silver; line-height: 7pt; margin-bottom: 2mm;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">見積有効期限</span>
                        @if(!empty($estimate['effective_date']))
                        {{ to_wareki($estimate['effective_date']) }} @else
                        発行日より45日以内
                        @endif
                    </div>

                    <div style="font-size: 13pt; font-weight: 400; border-bottom: 2px solid black; padding-left: 2mm; line-height: 13pt;">
                        <span style="display: inline-block; width: 25mm; font-size: 11pt; vertical-align: bottom; padding-bottom: 2px;">御見積金額</span>
                        @if($showPrice)
                        ￥{{ Illuminate\Support\Number::format($total ?? 0) }} -
                        @else
                        - 単価見積 -
                        @endif
                    </div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 40%; vertical-align: top;">
                    <div style="border-bottom: 1px solid silver; line-height: 7pt; margin-bottom: 2mm;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">見積番号</span>
                        {{ $estimate['estimate_number'] }}
                    </div>
                    <div style="border-bottom: 1px solid silver; line-height: 7pt; margin-bottom: 2mm;">
                        <span style="display: inline-block; width: 25mm; padding-left: 2mm; vertical-align: bottom; padding-bottom: 2px;">発行日</span>
                        @if($estimate['show_estimate_date'] == 1)
                        {{ to_wareki($estimate['created_date']) }} @else
                        &nbsp;
                        @endif
                    </div>
                    <div style="position: relative; padding-left: 2mm;">
                        <h4 style="font-size: 13pt; font-weight: 400; margin: 0 0 4mm 0 !important;">
                            <div style="height: 5mm; white-space: pre-wrap;">{{ $requester?->name }}</div>
                            <div style="height: 5mm; white-space: pre-wrap;">{{ $requester?->representative }}</div>
                        </h4>
                        @if(!empty($stampBase64))
                        <img class="insho" src="data:image/png;base64,{{ $stampBase64 }}" alt="印章">
                        @endif

                        <div style="height: 7mm;">登録番号：T4200005007974</div>
                        <div style="height: 5mm;">〒{{ $requester?->postal_code }}</div>
                        <div style="height: 5mm;">{{ $requester?->address }}</div>
                        <div style="height: 5mm;">Tel.{{ $requester?->phone }}</div>
                        <div style="height: 5mm;">Fax.{{ $requester?->fax }}</div>
                        @if($estimate['show_creator'] == 1)
                        担当者：{{ $creator?->name }}
                        @else
                        &nbsp;
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 52%;">
                        @if($estimate['estimate_category_id'] == 15)
                        品<div style="width: 5em !important; display: inline-block;"></div>名
                        @else
                        摘<div style="width: 5em !important; display: inline-block;"></div>要
                        @endif
                    </th>
                    <th style="width: 12%; text-align: center;">数量</th>
                    <th style="width: 12%; text-align: center;">単位</th>
                    <th style="width: 12%; text-align: center;">単価</th>
                    <th style="width: 12%;">金額</th>
                </tr>
            </thead>
            <tbody>
                @if($items->isNotEmpty())
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] ?? '' }}</td>
                    <td class="text-right">
                        {{ isset($item['quantity']) && $item['quantity'] !== '' ? Illuminate\Support\Number::format($item['quantity']) : '' }}
                    </td>
                    <td class="text-center">{{ $item['unit'] ?? '' }}</td>
                    <td class="text-right">
                        {{ isset($item['unit_price']) && $item['unit_price'] !== '' ? Illuminate\Support\Number::format($item['unit_price']) : '' }}
                    </td>
                    <td class="text-right">
                        {{ isset($item['quantity'], $item['unit_price']) && $item['quantity'] !== '' && $item['unit_price'] !== '' ? Illuminate\Support\Number::format((float)$item['quantity'] * (float)$item['unit_price']) : '' }}
                    </td>
                </tr>
                @endforeach
                @endif

                {{-- 空行をいくつか入れて高さを調整（任意） --}}
                @for($i = 0; $i < (15 - $items->count()); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" rowspan="3" style="border: none; vertical-align: top;position:relative">
                        <div style="height: 15px; line-height: 1; vertical-align: top;">備考：</div>
                        <div style="position:absolute;top:20px;left:0;padding:0.5em 0.7em;line-height:0.8;white-space: pre-wrap;overflow-wrap: break-word;word-break: break-word">{{ $estimate['remarks'] ?? '' }}</div>
                    </td>
                    <th>10%対象</th>
                    <td style="text-align: right;">
                        {{ $showPrice && optional($taxType)->rate == 10 ? Illuminate\Support\Number::format($subtotal ?? 0, 0) : '' }}
                    </td>
                    <td style="font-size: 8pt; position: relative; text-align: right;">
                        <span style="font-size: 5pt; position: absolute; top: 0; left: 5px;">
                            @if($showPrice && optional($taxType)->rate == 10)
                            @if($estimate['tax_type_id'] == 3)
                            内消費税
                            @elseif($estimate['tax_type_id'] == 1)
                            消費税
                            @endif
                            @endif
                        </span>
                        @if($showPrice && optional($taxType)->rate == 10)
                        {{ Illuminate\Support\Number::format($taxAmount ?? 0, 0) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>8%対象</th>
                    <td style="text-align: right;">
                        @if($showPrice && optional($taxType)->rate == 8)
                        {{ Illuminate\Support\Number::format($subtotal ?? 0, 0) }}
                        @endif
                    </td>
                    <td style="font-size: 8pt; position: relative; text-align: right;">
                        <span style="font-size: 5pt; position: absolute; top: 0; left: 5px;">
                            @if($showPrice && optional($taxType)->rate == 8)
                            @if($estimate['tax_type_id'] == 4)
                            内消費税
                            @elseif($estimate['tax_type_id'] == 2)
                            消費税
                            @endif
                            @endif
                        </span>
                        @if($showPrice && optional($taxType)->rate == 8)
                        {{ Illuminate\Support\Number::format($taxAmount ?? 0, 0) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>合計</th>
                    <td style="text-align: right;">
                        @if($showPrice)
                        {{ Illuminate\Support\Number::format($subtotal ?? 0, 0) }}
                        @endif
                    </td>
                    <td style="font-size: 8pt; position: relative; text-align: right;">
                        <span style="font-size: 5pt; position: absolute; top: 0; left: 5px;">
                            @if($showPrice)
                            @if(optional($taxType)->type == 'inclusive')
                            内消費税
                            @elseif(optional($taxType)->type == 'exclusive')
                            消費税
                            @endif
                            @endif
                        </span>
                        @if($showPrice)
                        {{ Illuminate\Support\Number::format($taxAmount ?? 0, 0) }}
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>