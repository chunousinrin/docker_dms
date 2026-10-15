<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\Shared\DailyReport;
use App\Models\Shared\DailyReportBusinessType;
use App\Models\Shared\DailyReportWeather;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DailyReportController extends Controller
{
    /**
     * 一覧はログインユーザー自身の日報のみ表示する。
     */
    public function index(Request $request)
    {
        $query = DailyReport::with(['amBusinessType', 'pmBusinessType', 'amWeather', 'pmWeather'])
            ->where('user_id', Auth::id())
            ->orderByDesc('work_date');

        if ($request->filled('work_date_from')) {
            $query->whereDate('work_date', '>=', $request->work_date_from);
        }
        if ($request->filled('work_date_to')) {
            $query->whereDate('work_date', '<=', $request->work_date_to);
        }

        $dailyReports = $query->paginate(20)->withQueryString();

        // 本日分がまだ未登録かどうか（一覧画面の「本日の日報を書く」ボタン制御に使用）
        $todayReport = DailyReport::where('user_id', Auth::id())
            ->whereDate('work_date', today())
            ->first();

        return view('daily_reports.index', compact('dailyReports', 'todayReport'));
    }

    public function create()
    {
        // 本日分が既に存在する場合は、新規作成ではなく編集画面へ誘導する
        $todayReport = DailyReport::where('user_id', Auth::id())
            ->whereDate('work_date', today())
            ->first();

        if ($todayReport) {
            return redirect()
                ->route('daily-reports.edit', $todayReport)
                ->with('info', '本日分の日報は既に登録されています。内容を編集してください。');
        }

        $businessTypes = DailyReportBusinessType::orderBy('id')->get();
        $weathers = DailyReportWeather::orderBy('id')->get();

        return view('daily_reports.create', compact('businessTypes', 'weathers'));
    }

    public function store(StoreDailyReportRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        DailyReport::create($data);

        return redirect()
            ->route('daily-reports.index')
            ->with('success', '日報を登録しました。');
    }

    public function show(DailyReport $dailyReport)
    {
        Gate::authorize('view', $dailyReport);

        $dailyReport->load(['amBusinessType', 'pmBusinessType', 'amWeather', 'pmWeather']);

        return view('daily_reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        Gate::authorize('update', $dailyReport);

        $businessTypes = DailyReportBusinessType::orderBy('name')->get();
        $weathers = DailyReportWeather::orderBy('id')->get();

        return view('daily_reports.edit', compact('dailyReport', 'businessTypes', 'weathers'));
    }

    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport)
    {
        Gate::authorize('update', $dailyReport);

        $dailyReport->update($request->validated());

        return redirect()
            ->route('daily-reports.index')
            ->with('success', '日報を更新しました。');
    }

    public function destroy(DailyReport $dailyReport)
    {
        Gate::authorize('delete', $dailyReport);

        $dailyReport->delete();

        return redirect()
            ->route('daily-reports.index')
            ->with('success', '日報を削除しました。');
    }
}
