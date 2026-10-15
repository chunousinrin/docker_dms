<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>Document</title>
    <style>
        /* フォント設定 */
        @font-face {
            font-family: "notosansjp";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Thin.ttf") }}') format("truetype");
            font-weight: 100;
        }

        @font-face {
            font-family: "notosansjp";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Light.ttf") }}') format("truetype");
            font-weight: 300;
        }

        @font-face {
            font-family: "notosansjp";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Regular.ttf") }}') format("truetype");
            font-weight: 400;
        }

        @font-face {
            font-family: "notosansjp";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Bold.ttf") }}') format("truetype");
            font-weight: 700;
        }

        @page {
            size: A4;
            margin: 20mm;
            /* 印刷用の余白を設定（必要に応じて調整してください） */
        }

        body {
            font-family: "notosansjp", sans-serif;
            font-weight: 300;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        /* 横並び用テーブルの共通スタイル */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .layout-table td {
            padding: 0 0.5em;
            vertical-align: bottom;
        }

        /* ユーティリティ */
        .text-right {
            text-align: right;
        }

        .office tr td {
            width: calc(100% / 3);
            font-size: 8.5pt;
        }
    </style>
</head>

<body>
    <header style="width:100%;">
        <table class="layout-table" style="margin-bottom: 3em;">
            <tr>
                <td style="width: 40%; border-bottom: 2px solid #023328;">
                    @php
                    // storage_path() を使って絶対パスを取得し、PHPの関数で読み込む
                    $path = storage_path('app/public/img/jforest.svg');
                    $logoBase64 = file_exists($path) ? base64_encode(file_get_contents($path)) : '';
                    @endphp

                    <img src="data:image/svg+xml;base64,{{ $logoBase64 }}" alt="ロゴ" style="width:21mm;height:auto;">
                </td>
                <td class="text-right" style="width: 60%; border-bottom: 2px solid #70bd29;">
                    {{ to_wareki($date) }}
                </td>
            </tr>
        </table>

        <table class="layout-table">
            <tr>
                <td style="width: 60%; font-size: 12pt; padding-bottom: 5px;vertical-align: top;padding:0;">
                    <div style="border-bottom: 1px solid #000;padding-left: 0.5em;">{{ $customer_name }}&nbsp;{{ $honorific }}</div>
                </td>
                <td style=" width: 5%;">
                </td>
                <td style="width: 35%; font-size: 9pt; text-align:right;">
                    <div style="display:inline-block;text-align:left;line-height: 0.9em;">
                        <div>中濃森林組合</div>
                        <div>岐阜県美濃市長瀬427番8</div>
                        @if($show_staff == 1)
                        <div>担当：{{ $staff_name }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <main style="text-align:center; margin-top: 30px;">
        <h1 style="font-weight:300; font-size: 18pt; margin-bottom: 30px;">文書送付のご案内</h1>

        @if($greeting)
        <div style="width:100%;text-align:left;">{{ mb_substr($greeting,0,2)}}</div>
        @endif

        <article style="text-align:left; line-height: 1.2;">
            {!! nl2br(e($body)) !!}
        </article>

        @if($greeting)
        <div style="width:100%;text-align:right;">{{ mb_substr($greeting,-2)}}</div>
        @endif

        @php
        // 有効な添付ファイル（nameが空でないもの）だけを事前に抽出
        $filteredAttachments = array_filter($attachments ?? [], fn($item) => !empty($item['name']));
        @endphp

        @if(!empty($filteredAttachments))
        <div style="text-align:center; margin-top: 40px; margin-bottom: 15px;">記</div>

        <article>
            <table style="border-collapse:collapse; margin:0 auto;">
                <tbody>
                    @foreach($filteredAttachments as $item)
                    <tr>
                        <td style="text-align: left;">{{ $item['name'] }}</td>
                        <td style="padding:0 2em;">・・・</td>
                        <td class="text-right">{{ $item['qty'] }}{{ $item['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </article>

        <div style="text-align:right; margin-top: 15px;">以上</div>
        @endif

    </main>
    <footer style="width:100%;position:absolute;bottom:0;left:0;">
        <div style="padding:0 0.5em;">中濃森林組合&emsp;<span>https://www.chunousinrin.or.jp</span></div>

        <table class="layout-table" style="margin:0;padding:0;">
            <tr>
                <td style="width:60%;height:0;border-bottom:2px solid rgba(112,189,41,1)"> </td>
                <td style="width:40%;height:0;border-bottom:2px solid rgba(2,51,40,1)"> </td>
            </tr>
        </table>


        <table class="layout-table office">
            <tr>
                <td>
                    <div>[　本　所　]</div>
                    <div>〒501-3782　岐阜県美濃市長瀬427番8</div>
                    <div>TEL 0575-35-3010　FAX 0575-31-0388</div>
                </td>
                <td style="text-align:center;">
                    <div style="display:inline-block; text-align:left;">
                        <div>[ 板取川支所 ]</div>
                        <div>〒501-2812　岐阜県関市洞戸市場876-2</div>
                        <div>TEL 0581-58-2156　FAX 0581-58-2490</div>
                    </div>
                </td>
                <td style="text-align:right;">
                    <div style="display:inline-block; text-align:left;">
                        <div>[ 津保川支所 ]</div>
                        <div>〒501-3601　岐阜県関市上之保145-1</div>
                        <div>TEL 0575-47-2009　FAX 0575-47-2009</div>
                    </div>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>