<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
        /* フォント定義 */
        @font-face {
            font-family: "notosansjp";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: "notosansjp_b";
            src: url('file://{{ storage_path("fonts/NotoSansJP-bold.ttf") }}') format("truetype");
            font-weight: bold;
        }

        @font-face {
            font-family: "notosansjp_t";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Thin.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: "notosansjp_l";
            src: url('file://{{ storage_path("fonts/NotoSansJP-Light.ttf") }}') format("truetype");
        }

        @page {
            size: A4;
            margin: 10mm 20mm;
            margin-bottom: 0;
            font-size: 10pt;
        }

        body {
            padding-bottom: 60mm;
        }

        * {
            border-collapse: collapse;
            font-family: notosansjp_l !important;
            font-weight: normal !important;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        footer {
            position: fixed;
            bottom: 7mm;
            font-size: 8pt;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <header>
        <div>
            <div style="float: left">No.{{ $ringi->document_no }}</div>
            <div style="float: right">{{ to_wareki($ringi->applied_date) }}</div>
            <div style="clear: both"></div>
        </div>
        <h1 style="margin: 0 auto;text-align: center;letter-spacing: 2em;margin-right: -2em;line-height: 16pt;">
            {{ $ringi->ringiType->name }}
        </h1>
        <div style="text-align: right; vertical-align: top;margin-bottom:1.5em;">
            <div style="display: inline-block;text-align: left;vertical-align: top;">
                起案者｜
            </div>
            <div style="display: inline-block;text-align: left;vertical-align: top;line-height:0.7;position:relative">
                {{ $ringi->user->department }}&emsp;{{ $ringi->user->section }}&emsp;{{ $ringi->user->position }}<br />{{ $ringi->user->name }}
                <?= '<img style="opacity:0.8;position:absolute;top:2mm;right:0;" alt="印鑑" width="30mm" height="30mm" src="data:image/svg+xml;base64,' . base64_encode($ringi->user->stamp) . '">' ?>
            </div>
        </div>
        <div style="border-top:2px solid #000;">
            <table style="border-collapse: collapse;width:100%;">
                <tr>
                    <td style="width:9%;padding-left:1em;height:15mm;line-height:9pt;">表題</td>
                    <td>
                        <h2 style="font-size:12pt;white-space: pre-wrap;overflow-wrap: break-word;word-break: break-word;line-height:9pt;">{{ $ringi->title }}</h2>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <main style="margin-bottom:1em;">
        <div style="border-top: 2px solid #000;padding: 0 1em;line-height: 1.2;box-sizing: border-box;
        white-space: pre-wrap;overflow-wrap: break-word;word-break: break-word;;">{!! e($ringi->content) !!}</div>
    </main>

    <div style="page-break-inside: avoid;width:100%;position:absolute;bottom:13.5mm;left:0;">
        <div style="border-top: 2px solid #000; width: 100%;">
            <table style="border-collapse: collapse;width:100%;">
                <tr>
                    <td style="width:9%;padding-left:1em;height:13mm;">
                        <div style="line-height:8pt;">添付<br>書類</div>
                    </td>
                    <td style="vertical-align:top;">
                        @forelse($ringi->attachmentNames ?? [] as $name)
                        ◇{{ $name }}&emsp;
                        @empty

                        @endforelse
                    </td>
                </tr>
            </table>
        </div>
        <div style="border-top: 2px solid #000; width: 100%;">
            <table style="border-collapse: collapse;width:100%;">
                <tr>
                    <td style="width:9%;padding-left:1em;height:10mm;vertical-align:top">ｺﾒﾝﾄ </td>
                    <td style="vertical-align:top;"> </td>
                </tr>
            </table>

        </div>
        <table style="width: 100%;border-collapse: collapse;border: 1px solid silver;margin-top:2em;">
            <tr>
                <td style="width: 5mm; text-align: center;padding:1em 0;">決<br />裁</td>
                <td style="width: 20mm;vertical-align: top;border-left: 1px solid silver;padding: 0.1em 0.5em;">
                    組合長
                </td>
                <td style="width: 20mm;vertical-align: top;border-left: 1px solid silver;padding: 0.1em 0.5em;">
                    専務
                </td>
                <td style="vertical-align: top;border-left: 1px solid silver;padding: 0.1em 0.5em;">
                    閲覧
                </td>
            </tr>
        </table>

    </div>
    <footer style="width:100%;text-align:center;"> p. <span class="page-number"></span></footer>
</body>

</html>