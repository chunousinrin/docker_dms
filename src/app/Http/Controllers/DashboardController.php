<?php

namespace App\Http\Controllers;

use App\Models\WorkerV2\AttendanceV2;
use App\Models\Request\RingiRequest;

class DashboardController extends Controller
{
    /**
     * Dashboard
     */
    public function index()
    {
        /*
    |--------------------------------------------------------------------------
    | 稟議書承認状況
    |--------------------------------------------------------------------------
    */
        $approveCount = RingiRequest::whereHas('steps', function ($q) {
            $q->where('approver_id', auth()->id())
                ->whereColumn(
                    'approval_steps.step_order',
                    'ringi_requests.current_step'
                )
                ->where('status', 'pending');
        })->count();

        $returnedCount = RingiRequest::where(
            'user_id',
            auth()->id()
        )->where(
            'status',
            RingiRequest::STATUS_RETURNED
        )->count();

        $draftCount = RingiRequest::where(
            'user_id',
            auth()->id()
        )->where(
            'status',
            RingiRequest::STATUS_DRAFT
        )->count();

        /*
    |--------------------------------------------------------------------------
    | 今日の勤怠データの取得 (Eloquent)
    |--------------------------------------------------------------------------
    */
        $todayAttendances = AttendanceV2::with(['worker', 'group'])
            ->whereDate('work_date', today())
            ->orderBy('worker_id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 勤怠データの集計・フィルタリング
    |--------------------------------------------------------------------------
    */
        $totalCount = $todayAttendances->count();

        $workingCount = $todayAttendances
            ->whereNull('clock_out')
            ->count();

        $completedCount = $todayAttendances
            ->whereNotNull('clock_out')
            ->count();

        $commentCount = $todayAttendances
            ->filter(function ($attendance) {
                return !empty(trim($attendance->comment ?? ''));
            })
            ->count();

        // 未退勤一覧
        $workingAttendances = $todayAttendances
            ->whereNull('clock_out');

        // コメント有一覧
        $commentAttendances = $todayAttendances
            ->filter(function ($attendance) {
                return !empty(trim($attendance->comment ?? ''));
            });

        /*
    |--------------------------------------------------------------------------
    | ビューへの返却
    |--------------------------------------------------------------------------
    */
        return view('dashboard', compact(
            'todayAttendances',
            'totalCount',
            'workingCount',
            'completedCount',
            'commentCount',
            'workingAttendances',
            'commentAttendances',
            'approveCount',
            'returnedCount',
            'draftCount'
        ));
    }
    public function attendanceWidget()
    {
        $todayAttendances = AttendanceV2::with(['worker', 'group'])
            ->whereDate('work_date', today())
            ->orderBy('worker_id')
            ->get();

        $totalCount = $todayAttendances->count();

        $workingAttendances = $todayAttendances
            ->whereNull('clock_out');

        $workingCount = $workingAttendances->count();

        return view('widgets.attendance', compact(
            'totalCount',
            'workingCount',
            'workingAttendances'
        ));
    }
}
