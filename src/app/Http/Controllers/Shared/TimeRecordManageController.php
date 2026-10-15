<?php
// app/Http/Controllers/Shared/TimeRecordManageController.php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Master\User;
use App\Models\Shared\TimeRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class TimeRecordManageController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('used', 1)->orderBy('id')->get();

        $timeRecords = $this->buildQuery($request)
            ->orderBy('work_date', 'desc')
            ->orderBy('user_id')
            ->paginate(20)
            ->withQueryString();

        return view('time_records.manage.index', compact('timeRecords', 'users'));
    }

    public function export(Request $request)
    {
        $timeRecords = $this->buildQuery($request)
            ->orderBy('work_date')
            ->orderBy('user_id')
            ->get();

        $filename = 'time_records_' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($timeRecords) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");

            fputcsv($stream, [
                '従業員',
                '勤務日',
                '勤務開始時間',
                '時間内退勤',
                '時間内出勤',
                '勤務終了時間',
                '備考',
            ]);

            foreach ($timeRecords as $record) {
                fputcsv($stream, [
                    $record->user->name ?? '',
                    optional($record->work_date)->format('Y-m-d'),
                    $record->start_time ? Carbon::parse($record->start_time)->format('H:i') : '',
                    $record->break_start_time ? Carbon::parse($record->break_start_time)->format('H:i') : '',
                    $record->break_end_time ? Carbon::parse($record->break_end_time)->format('H:i') : '',
                    $record->end_time ? Carbon::parse($record->end_time)->format('H:i') : '',
                    $record->note,
                ]);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // PDF出力(期間内の全日付を表示、土日・未打刻日も空欄で色分け)
    public function pdf(Request $request)
    {
        // 期間未指定の場合は当月をデフォルトにする
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))->startOfDay()
            : Carbon::now()->endOfMonth();

        // 対象ユーザー(user_id指定があれば1名、なければ在籍中の全員)
        if ($request->filled('user_id')) {
            $targetUsers = User::where('id', $request->input('user_id'))->get();
        } else {
            $targetUsers = User::where('used', 1)->orderBy('name')->get();
        }

        $isSingleUser = $targetUsers->count() === 1;
        $userName = $isSingleUser ? $targetUsers->first()->name : null;

        // 期間内の既存打刻データを取得し、[user_id][work_date] => record の形にまとめる
        $existingRecords = TimeRecord::query()
            ->whereIn('user_id', $targetUsers->pluck('id'))
            ->whereDate('work_date', '>=', $dateFrom->toDateString())
            ->whereDate('work_date', '<=', $dateTo->toDateString())
            ->get()
            ->groupBy('user_id')
            ->map(function ($records) {
                return $records->keyBy(function ($record) {
                    return Carbon::parse($record->work_date)->toDateString();
                });
            });

        // 期間内の全日付を生成
        $period = CarbonPeriod::create($dateFrom, $dateTo);

        // ユーザー×日付 の全組み合わせを生成(打刻がなければnull)
        $rows = collect();
        foreach ($targetUsers as $user) {
            $userRecords = $existingRecords->get($user->id, collect());

            foreach ($period as $date) {
                $dateKey = $date->toDateString();
                $rows->push([
                    'date'   => $date->copy(),
                    'user'   => $user,
                    'record' => $userRecords->get($dateKey), // なければnull
                ]);
            }
        }

        // 日付順→ユーザー順(複数ユーザーの場合)にソート
        $rows = $rows->sortBy([
            ['date', 'asc'],
            fn($a, $b) => $a['user']->id <=> $b['user']->id,
        ])->values();

        $pdf = Pdf::loadView('time_records.manage.pdf', [
            'rows'         => $rows,
            'userName'     => $userName,
            'isSingleUser' => $isSingleUser,
            'dateFrom'     => $dateFrom->format('Y-m-d'),
            'dateTo'       => $dateTo->format('Y-m-d'),
        ])->setPaper('a4', 'landscape');

        $filename = 'time_records_' . now()->format('YmdHis') . '.pdf';

        return $pdf->stream($filename);
    }

    private function buildQuery(Request $request)
    {
        return TimeRecord::query()
            ->with('user')
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->input('user_id'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('work_date', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('work_date', '<=', $request->input('date_to'));
            });
    }
}
