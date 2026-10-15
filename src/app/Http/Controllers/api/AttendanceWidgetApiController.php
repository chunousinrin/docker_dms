<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkerV2\WorkerV2;
use Illuminate\Http\JsonResponse;

class AttendanceWidgetApiController extends Controller
{
    /**
     * 勤怠ウィジェットAPI
     */
    public function index(): JsonResponse
    {
        // 今日
        $today = today();

        // 在籍作業員 + 今日の勤怠
        $workers = WorkerV2::with([
            // 今日の勤怠
            'todayAttendance' => function ($q) use ($today) {
                $q->whereDate('work_date', $today);
            },
            // 今日の班
            'todayAttendance.group'
        ])
            // 在籍のみ
            ->where('is_active', true)
            // 並び順
            ->orderBy('id')
            ->get();

        // 集計
        // 総人数
        $totalCount = $workers->count();

        // 未退勤
        $workingCount = $workers->filter(function ($worker) {
            return $worker->todayAttendance && !$worker->todayAttendance->clock_out;
        })->count();

        // 未打刻
        $missingCount = $workers->whereNull('todayAttendance')->count();

        // worker data
        $workerData = $workers->map(function ($worker) {
            $attendance = $worker->todayAttendance;

            // status
            if (!$attendance) {
                $status = 'missing';
            } elseif (!$attendance->clock_out) {
                $status = 'working';
            } else {
                $status = 'completed';
            }

            // response row
            return [
                'worker_id' => $worker->id,
                'name'      => $worker->name,
                'group'     => $attendance->group->name ?? '',
                'clock_in'  => optional($attendance?->clock_in)->format('H:i'),
                'clock_out' => optional($attendance?->clock_out)->format('H:i'),
                'status'    => $status,
            ];
        })->values();

        // JSON response
        return response()->json([
            'totalCount'   => $totalCount,
            'workingCount' => $workingCount,
            'missingCount' => $missingCount,
            'workers'      => $workerData,
            'updated_at'   => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
