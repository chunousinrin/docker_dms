<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            font-weight: 400;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            box-sizing: border-box;
            font-size: 10pt;
            line-height: .8rem;
        }

        body {
            margin: 12mm 15mm !important;
            padding: 0 !important;
        }

        .t-c {
            text-align: center;
        }

        .t-l {
            text-align: left;
        }

        .t-r {
            text-align: right;
        }

        .m-0 {
            margin: 0 !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .table-p {
            width: 100% !important;
            border-collapse: collapse !important;
            box-sizing: border-box !important;
        }

        .main th,
        .main td {
            padding: .6rem .5rem !important;
        }

        .main thead tr th:nth-of-type(n+1):nth-of-type(-n+5),
        .main tbody tr td:nth-of-type(n+1):nth-of-type(-n+5),
        .br-d {
            border-right: 1px dotted #6b7b6e;
        }

        .bb-s {
            border-bottom: 1px solid #6b7b6e;
        }

        .insho {
            position: absolute;
            top: 0;
            right: 2mm;
            width: 19mm;
            opacity: 0.65;
        }
    </style>
</head>

<body>
    <header>
        <table class="table-p" style="border-bottom: 2px solid #000;margin-bottom:3rem;">
            <tr>
                <td rowspan="2" style="width:auto;padding:0 0.5rem;vertical-align:bottom;padding-bottom:.1rem">
                    <h2 style="font-size:2rem;">発 注 書</h2>
                </td>
                <td style="width: 0;white-space:nowrap;padding:0;">発注番号：</td>
                <td style="width: 0;white-space:nowrap;padding:0;padding-right:.5rem;">{{ $purchaseOrder->order_number }}</td>
            </tr>
            <tr>
                <td style="width: 0;white-space:nowrap;">発注日：</td>
                <td style="width: 0;white-space:nowrap;padding:0;padding-right:.5rem;">{{ optional($purchaseOrder->order_date)->format('Y年m月d日') }}</td>
            </tr>
        </table>

        <table class="table-p" style="margin-bottom: 3rem;">
            <tr>
                <td style="max-width: 50%; margin-right:5em;">
                    <h3 class="bb-s" style="font-size:1.75rem;padding:0 1.25rem 0 .5rem;display:inline-block">
                        {{ $purchaseOrder->supplier_name }}
                        {{ $purchaseOrder->supplier_title }}
                    </h3>
                    @if($purchaseOrder->supplier_address)
                    <div style="padding: 0 .5rem;"> {{ $purchaseOrder->supplier_address }} </div>
                    @endif
                    <div style="padding: 0 .5rem;">
                        {{ $purchaseOrder->supplier_department }}
                        {{ $purchaseOrder->supplier_person }}
                        @if($purchaseOrder->supplier_person)
                        {{ $purchaseOrder->supplier_person_title }}
                        @endif
                </td>
                <td class="t-r">
                    <div style="display: inline-block;padding-right:.5rem;position:relative;">
                        <h5 class="t-l" style="font-size: 1.25rem;">{{ $purchaseOrder->branch->name }}</h5>
                        <div class="t-l">{{ $purchaseOrder->branch->address }} </div>
                        <div class="t-l" style="font-size: 80%;">Tel.{{ $purchaseOrder->branch->phone }} Fax.{{ $purchaseOrder->branch->fax }}</div>
                        <div class="t-l">担当者：{{ $purchaseOrder->user->name }} </div>
                        @if(!empty($stampBase64))
                        <img class="insho" src="data:image/png;base64,{{ $stampBase64 }}" alt="印章">
                        @endif

                    </div>
                </td>
            </tr>
        </table>

        <div style="padding: 0 .5rem; margin-bottom:.5rem;">平素は格別のご高配を賜り、厚く御礼申し上げます。下記の通り発注申し上げます。</div>

        <table class="table-p" style="border:1px solid #6b7b6e;margin-bottom:1.5rem;">
            <tr>
                <td style="padding: 1rem;padding-bottom:.8rem;vertical-align:bottom;">
                    <h4 style="font-size: 1.5rem">税込合計金額</h4>
                </td>
                <td class="t-r" style="padding: 1rem;padding-bottom:.8rem;vertical-align:bottom;">
                    <h4 style="font-size: 1.5rem">￥{{ number_format($purchaseOrder->total_amount) }} -</h4>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <table class="table-p main">
            <thead>
                <tr class="t-c" style="border-top:3px double #6b7b6e;border-bottom:3px double #6b7b6e;">
                    <th class="t-c " style="width:0;white-space:wrap;">No</th>
                    <th class="" style="">品名・項目</th>
                    <th class="" style="width:90px;">数量</th>
                    <th class="" style="width:70px;">単位</th>
                    <th class="" style="width:120px;">単価</th>
                    <th class="" style="width:120px;">金額</th>
                </tr>
            </thead>
            <tbody>
                @php
                $items = $purchaseOrder->items->keyBy('order_number');
                @endphp

                @for($i = 1; $i <= 15; $i++)
                    @php
                    $item=$items->get($i);
                    @endphp
                    <tr class="bb-s">
                        <td class="t-c">
                            {{ $i }}
                        </td>
                        <td>
                            {{ $item?->item_name }}
                        </td>
                        <td class="t-r">
                            {{ $item ? number_format($item?->quantity,0) : '' }}
                        </td>
                        <td class="t-c">
                            {{ $item?->unit }}
                        </td>
                        <td class="t-r">
                            {{ $item ? number_format($item->unit_price) : '' }}
                        </td>
                        <td class="t-r">
                            {{ $item ? number_format($item->amount) : '' }}
                        </td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" rowspan="6" style="position:relative">
                        <div style="position: absolute;top:2px;left:0;width:100%;">
                            <div class="py-1 px-2" style="padding: .5rem;">備考：</div>
                            <div class="px-2" style="padding: 0 .5rem;">{!! nl2br(e($purchaseOrder->remarks)) !!} </div>
                        </div>
                    </td>
                    <td></td>
                    <td class="px-2 br-d bb-s">小計(税抜)</td>
                    <td class="px-2 t-r bb-s">{{ number_format($purchaseOrder->subtotal) }} </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="px-2 br-d bb-s">消費税</td>
                    <td class="px-2 t-r bb-s">{{ number_format($purchaseOrder->tax_amount) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="px-2 br-d bb-s"> 合計金額 </td>
                    <td class="px-2 t-r bb-s"> {{ number_format($purchaseOrder->total_amount) }} </td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>