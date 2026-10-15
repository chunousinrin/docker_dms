<!DOCTYPE html>
<html lang="ja">

<head>

    <meta charset="UTF-8">

    <meta
        http-equiv="refresh"
        content="30">

    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

</head>

<body class="p-2">

    <div class="card bg-info text-white">
        <div class="card-header d-flex align-items-center">
            <span>作業員出勤状況</span>
            <a href="{{ route('attendance_v2.index') }}" class="ml-auto text-white" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-external-link-square-alt"></i>
            </a>
        </div>

        <div class="card-body position-relative">
            {{-- 背景アイコン --}}
            <div class="position-absolute" style="right:20px; top:10px; opacity:0.15; font-size:70px;">
                <i class="fas fa-users"></i>
            </div>

            <div class="d-flex border-bottom py-1">
                <span>出勤人数</span>
                <h4 class="ml-auto mb-0">{{ $totalCount }}</h4>
            </div>

            <div class="d-flex py-1">
                <span>未退勤</span>
                <h4 class="ml-auto mb-0">{{ $workingCount }}</h4>
            </div>
        </div>

        <div class="card-footer p-0">
            <details>
                <summary class="px-4 py-3">未退勤者一覧</summary>
                <div style="max-height:250px; overflow:auto;">
                    <table class="table table-sm bg-white mb-0">
                        <thead>
                            <tr>
                                <th class="pl-2">氏名</th>
                                <th class="text-center">出勤時刻</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workingAttendances as $attendance)
                            <tr>
                                <td class="pl-2">
                                    {{ $attendance->worker->name ?? '' }}
                                </td>
                                <td class="text-center">
                                    {{ optional($attendance->clock_in)->format('H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">全員退勤済です</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </div>

</body>

</html>