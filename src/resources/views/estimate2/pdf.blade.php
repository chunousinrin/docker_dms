<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>見積書</title>
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

        * {
            font-family: 'NotoSansJP';
            font-weight: 400;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }


        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 10mm 15mm;
        }

        .summary thead tr td,
        .summary thead tr th {
            background-color: rgba(112, 189, 41, 0.5) !important;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td,
        .summary th {
            height: 20pt !important;
            line-height: 8pt !important;
            border: 1px solid silver;
            padding: 0 0.5em;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <table style="width: 100%;margin-bottom: 5mm;">
        <tr>
            <td style="width: 50%;vertical-align: top;">
                <h1 class="text-center" style="padding:0;border-bottom:3px double black;margin-bottom:5mm;height:27px;line-height:25px;">
                    <span style="display: inline-block;width:30px;font-size: 13pt;">御</span>
                    <span style="display: inline-block;width:30px;font-size: 13pt;">見</span>
                    <span style="display: inline-block;width:30px;font-size: 13pt;">積</span>
                    <span style="display: inline-block;width:30px;font-size: 13pt;">書</span>
                </h1>
                <div style="font-size: 13pt;border-bottom:1px solid black;padding-left: 2mm;line-height:8pt;white-space:pre-wrap;">{!! str_replace(['　', ' '], '&emsp;', $data['customer_name']) !!}&emsp;{{ $data['honorific'] }}</div>
                <div style="margin-bottom: 5mm;padding-left: 2mm;">下記のとおり、御見積申し上げます。</div>
                <div style="border-bottom: 1px solid silver;margin-bottom: 2mm;line-height:10pt;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">業務名称</span>
                    {{$data['biz_name']}}
                </div>
                <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">業務場所</span>
                    {{$data['location']}}
                </div>
                <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">工種</span>
                    {{$data['works_type']}}
                </div>
                <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">業務期間</span>
                    {{ (new IntlDateFormatter(
                                'ja_JP@calendar=japanese',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::NONE,
                                'Asia/Tokyo',
                                IntlDateFormatter::TRADITIONAL
                            ))->format(new DateTime($data['works_period_start'])) }}

                    @if(!empty($data['works_period_end']))
                    ～
                    {{ (new IntlDateFormatter(
                                'ja_JP@calendar=japanese',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::NONE,
                                'Asia/Tokyo',
                                IntlDateFormatter::TRADITIONAL
                            ))->format(new DateTime($data['works_period_end'])) }}
                    @endif
                    &nbsp;
                </div>

                <div style="font-size: 13pt;font-weight:400;border-bottom:2px solid black;padding-left: 2mm;line-height:13pt;">
                    <span style=" display:inline-block;width:25mm;font-size:11pt;vertical-align:bottom;padding-bottom:2px;">御見積金額</span>
                    @if($data['total_unit_price']!=1)
                    ￥{{ number_format($data['ottotal']) }} -
                    @else
                    - 単価見積 -
                    @endif
                    @if($data['tax_rate']!=1)
                    <span style='font-size:8pt;'>(消費税込)</span>
                    @else
                    <span style='font-size:8pt;'>(消費税別)</span>
                    @endif
                </div>
                <div style="padding-left: 2mm;">
                    見積有効期限：
                    @if(empty($data['effective_date']))
                    ご請求日より45日以内
                    @else
                    {{ (new IntlDateFormatter(
                            'ja_JP@calendar=japanese',
                            IntlDateFormatter::LONG,
                            IntlDateFormatter::NONE,
                            'Asia/Tokyo',
                            IntlDateFormatter::TRADITIONAL
                        ))->format(new DateTime($data['effective_date'])) }}
                    @endif
                </div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 40%;vertical-align: top;">
                <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 2mm;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px">見積番号</span>
                    {{ $data['estimate_number'] ?? '見積番号' }}
                </div>
                <div style="border-bottom: 1px solid silver;line-height:7pt;margin-bottom: 5mm;">
                    <span style="display:inline-block;width: 25mm;padding-left: 2mm;vertical-align:bottom;padding-bottom:2px;">発行日</span>
                    @if($data['cd_display']==1)
                    {{ (new IntlDateFormatter(
                                'ja_JP@calendar=japanese',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::NONE,
                                'Asia/Tokyo',
                                IntlDateFormatter::TRADITIONAL
                            ))->format(new DateTime($data['created_date'] ?? now())) }}
                    @endif
                </div>
                <div style="position:relative;">
                    <h4 style="text-align:left;font-size: 13pt;margin-bottom:2mm;line-height:10pt;font-weight:400;white-space:pre-wrap;">{!! str_replace(['　', ' '], '&emsp;', $data['branch_name']) !!}<br>{{ $data['branch_representative'] ?? '代表理事組合長　長尾 嘉市' }}</h4>
                    <div style="margin-bottom: 2mm;">登録番号：{{ $data['branch_registration_number'] }}</div>
                    <div style="line-height: 9pt;">〒{{ $data['branch_postal_code'] }}</div>
                    <div style="line-height: 9pt;">{{ $data['branch_address'] }}</div>
                    <div style="line-height: 9pt;">Tel.{{ $data['branch_phone'] }}</div>
                    <div style="line-height: 9pt;">Fax.{{ $data['branch_fax'] }}</div>
                    @if(!empty($data['branch_seal']))
                    <div style="position: absolute; top: 0; right: 2mm; z-index: 2;">
                        <img src="{{ $data['branch_seal'] }}" style="width: 19mm; height: auto; opacity: 0.65;">
                    </div>
                    @endif
                    @if($data['staff_display']=="1") 担当者：{{ $data['user_name'] }} @endif
                </div>
            </td>
        </tr>
    </table>
    <table class="summary">
        <thead>
            <tr>
                <th class="text-center" style="width: 40%;white-space:pre-wrap;">品&emsp;名&emsp;・&emsp;規&emsp;格&emsp;・&emsp;仕&emsp;様&emsp;等</th>
                <th class="text-center" style="width: 10%;">数量</th>
                <th class="text-center" style="width: 10%;">単位</th>
                <th class="text-center" style="width: 10%;">単価</th>
                <th class="text-center" style="width: 10%;">金額</th>
                <th class="text-center" style="width: 20%;">摘要</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td style="white-space: pre-wrap;">{!! str_replace(['　', ' '], '&emsp;', $item['summary']) !!}</td>
                <td class=" text-center">{{ !is_null($item['quantity']) ? number_format($item['quantity'], 2) : '' }}
                </td>
                <td class="text-center">{{ $item['unit'] }}</td>
                <td class="text-right">{{ !is_null($item['unit_price']) ? number_format($item['unit_price']) : ''}}</td>
                <td class="text-right">{{ !is_null($item['amount']) ? number_format($item['amount']) : '' }}</td>
                <td>{{ $item['remark'] }}</td>
            </tr>
            @endforeach

            {{-- 直接作業代 (A) --}}
            <tr class="table-secondary">
                <td>直接作業代小計</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">
                    {{ !is_null($data['subtotal']) ? number_format($data['subtotal']) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">小計</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- 諸経費1 (C) --}}
            <tr>
                <td>{{ $data['idsmr1'] }}</td>
                <td class="text-center">
                    {{ !is_null($data['idqat1']) ? number_format($data['idqat1'], 2) : '' }}
                </td>
                <td class="text-center">
                    {{ !is_null($data['idunt1']) ? $data['idunt1'] : '' }}
                </td>
                <td></td>
                <td class="text-right">
                    {{ !is_null($data['idamt1']) ? number_format($data['idamt1']) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">A×B</td> {{-- ←ここを表示 --}}
            </tr>

            {{-- 諸経費2 (E) --}}
            <tr>
                <td>{{ $data['idsmr2'] }}</td>
                <td class="text-center">
                    {{ !is_null($data['idqat2']) ? number_format($data['idqat2'], 2) : '' }}
                </td>
                <td class="text-center">
                    {{ !is_null($data['idunt2']) ? $data['idunt2'] : '' }}
                </td>
                <td></td>
                <td class="text-right">
                    {{ !is_null($data['idamt2']) ? number_format($data['idamt2']) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">(A+C)×D</td> {{-- ←ここを表示 --}}
            </tr>

            {{-- 工事原価 (F) --}}
            <tr class="table-secondary">
                <td>工事原価 (F)</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">
                    {{ !is_null($data['idtotal']) ? number_format($data['idtotal']) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">A+C+E</td> {{-- ←ここを表示 --}}
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- 諸経費3 (H) --}}
            <tr>
                <td>{{ $data['otsmr1'] }}</td>
                <td class="text-center">
                    {{ !is_null($data['otqat1']) ? number_format($data['otqat1'], 2) : '' }}
                </td>
                <td class="text-center">
                    {{ !is_null($data['otunt1']) ? $data['otunt1'] : '' }}
                </td>
                <td></td>
                <td class="text-right">
                    {{ !is_null($data['otamt1']) ? number_format($data['otamt1']) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">F×G</td> {{-- ←ここを表示 --}}
            </tr>

            {{-- その他経費 I, J, K --}}
            @foreach(['I' => 2, 'J' => 3, 'K' => 4] as $label => $idx)
            <tr>
                <td>{{ $data['otsmr'.$idx] ?? '' }}</td>
                <td class="text-center">
                    {{ !is_null($data['otqat'.$idx]) ? number_format($data['otqat'.$idx], 2) : '' }}
                </td>
                <td class="text-center">{{ $data['otunt'.$idx] ?? '' }}</td>
                <td class="text-right">
                    {{ !is_null($data['otup'.$idx]) ? number_format($data['otup'.$idx]) : '' }}
                </td>
                <td class="text-right">
                    {{ !is_null($data['otamt'.$idx]) ? number_format($data['otamt'.$idx]) : '' }}
                </td>
                <td class="text-center" style="font-size: 0.8em; color: #666;">{{ $label }}</td> {{-- ← I, J, K を表示 --}}
            </tr>
            @endforeach
            {{-- 最終合計 --}}
            {{-- 消費税 (Kの次) --}}
            <tr>
                @php
                // 税抜き合計を計算（idtotal + 諸経費3 + その他経費）
                $taxableAmount = ($data['idtotal'] ?? 0) + ($data['otamt1'] ?? 0) + ($data['otamt2'] ?? 0) + ($data['otamt3'] ?? 0) + ($data['otamt4'] ?? 0);
                $taxRate = ($data['tax_rate'] ?? 10) / 100;
                $taxAmount = floor($taxableAmount * $taxRate);
                @endphp
                {{ $taxAmount > 0 ? number_format($taxAmount) : '' }}

                <td> @if($data['tax_rate']!=1) 消費税 @endif </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">@if($data['tax_rate']!=1) {{ $taxAmount > 0 ? number_format($taxAmount) : '' }} @endif</td>
                <td></td>
            </tr>

            {{-- 最終合計 (L) --}}
            <tr>
                <td colspan="2" style="border:none;">備考：</td>
                <td colspan="2" class="text-center" style="background-color: rgba(112, 189, 41, 0.5) !important;white-space:pre-wrap;">合&emsp;&emsp;計</td>
                <td class="text-right">
                    {{ !is_null($data['ottotal']) && $data['ottotal'] > 0 ? number_format($data['ottotal']) : '' }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" style="border:none;">
                    {!! nl2br(e($data['Remark'] ?? '')) !!}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>