<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
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

        * {
            border-collapse: collapse;
            font-family: notosansjp_l !important;
            font-weight: normal !important;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .condition {
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 2px 6px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        td.note {
            text-align: left;
        }

        /* 日曜日のセル（薄い赤系） */
        td.sunday {
            background-color: #fff0f0 !important;
        }

        /* 土曜日のセル（薄い青系） */
        td.saturday {
            background-color: #f0f8ff !important;
        }
    </style>
</head>

<body>
    @php
    use Carbon\Carbon;

    // 文字列の場合は Carbon::parse($dateFrom) または new DateTime($dateFrom) にしてください
    $dateFromCarbon = Carbon::parse($dateFrom);

    $formatter = new IntlDateFormatter(
    'ja_JP@calendar=japanese',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'Asia/Tokyo',
    IntlDateFormatter::TRADITIONAL,
    'Gy年M月'
    );

    $dateFromFormatted = $formatter->format($dateFromCarbon);
    @endphp

    <h1>{{ $dateFromFormatted }}　{{ $userName ?: '全員' }}　出退勤一覧</h1>
    <table>
        <thead>
            <tr>
                <th style="width:{{ $isSingleUser ? '15%' : '12%' }};">勤務日</th>
                @unless($isSingleUser)
                @endunless
                <th style="width:12%;">勤務開始</th>
                <th style="width:12%;">時間内退勤</th>
                <th style="width:12%;">時間内出勤</th>
                <th style="width:12%;">勤務終了</th>
                <th style="width:12%;">勤務時間</th>
                <th>備考</th>
            </tr>
        </thead>

        <tbody>
            @forelse($rows as $row)
            @php
            $date = $row['date'];
            $record = $row['record'];
            $dow = $date->dayOfWeek; // 0:日 6:土

            // 土日用のクラス
            $dowClass = $dow === 0 ? 'sunday' : ($dow === 6 ? 'saturday' : '');

            // 勤怠がない場合のクラス
            $rowClass = $record ? '' : 'no-record';

            // 勤務時間
            $workTime = '-';

            if (
            $record &&
            $record->start_time &&
            $record->end_time
            ) {
            // 出勤時間
            $startTime = \Carbon\Carbon::parse($record->start_time);

            // 9:00より前なら9:00に丸める
            $baseStartTime = $startTime->copy()->setTime(9, 0, 0);

            if ($startTime->lt($baseStartTime)) {
            $startTime = $baseStartTime;
            }

            // 退勤時間
            $endTime = \Carbon\Carbon::parse($record->end_time);

            // 勤務時間を秒で取得
            $workSeconds = $startTime->diffInSeconds($endTime);

            // 時間内休憩を減算
            if (
            $record->break_start_time &&
            $record->break_end_time
            ) {
            $breakStartTime = \Carbon\Carbon::parse($record->break_start_time);
            $breakEndTime = \Carbon\Carbon::parse($record->break_end_time);

            // 時間内休憩の秒数
            if ($breakStartTime->lt($breakEndTime)) {
            $breakSeconds = $breakStartTime->diffInSeconds($breakEndTime);

            $workSeconds -= $breakSeconds;
            }
            }

            // マイナスにならないようにする
            $workSeconds = max(0, $workSeconds);

            // HH:mm:ss に変換
            $hours = intdiv($workSeconds, 3600);
            $minutes = intdiv($workSeconds % 3600, 60);
            $seconds = $workSeconds % 60;

            $workTime = sprintf(
            '%02d:%02d',
            $hours,
            $minutes
            );
            }
            @endphp

            <tr class="{{ $rowClass }}">
                <td class="{{ $dowClass }}">
                    {{ $date->locale('ja')->isoFormat('MM-DD (ddd)') }}
                </td>

                @unless($isSingleUser)
                <td class="{{ $dowClass }}">
                    {{ $row['user']->name }}
                </td>
                @endunless

                <td class="{{ $dowClass }}">
                    {{ $record && $record->start_time
                    ? \Carbon\Carbon::parse($record->start_time)->format('H:i')
                    : '-' }}
                </td>

                <td class="{{ $dowClass }}">
                    {{ $record && $record->break_start_time
                    ? \Carbon\Carbon::parse($record->break_start_time)->format('H:i')
                    : '-' }}
                </td>

                <td class="{{ $dowClass }}">
                    {{ $record && $record->break_end_time
                    ? \Carbon\Carbon::parse($record->break_end_time)->format('H:i')
                    : '-' }}
                </td>

                <td class="{{ $dowClass }}">
                    {{ $record && $record->end_time
                    ? \Carbon\Carbon::parse($record->end_time)->format('H:i')
                    : '-' }}
                </td>

                {{-- 勤務時間 --}}
                <td class="{{ $dowClass }}">
                    {{ $workTime }}
                </td>

                <td class="note {{ $dowClass }}">
                    {{ $record->note ?? '' }}
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="{{ $isSingleUser ? 7 : 8 }}">
                    該当する期間・従業員がありません
                </td>
            </tr>
            @endforelse
        </tbody>

    </table>
</body>

</html>