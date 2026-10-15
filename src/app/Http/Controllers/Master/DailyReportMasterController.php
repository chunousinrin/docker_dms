<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Shared\DailyReport;
use App\Models\Master\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DailyReportMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dailyReports = DailyReport::orderBy('work_date', 'desc')->orderBy('id')->paginate(15);
        $users = User::orderBy('id')->get(); // 作成者（ユーザー）
        $businessTypes = DB::table('daily_report_business_types')->get();
        $weathers = DB::table('daily_report_weathers')->get();
        return view('master.daily_reports.index', compact('dailyReports', 'users', 'businessTypes', 'weathers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'work_date' => 'required|date',
            'am_business_type_id' => 'required',
            'am_summary' => 'nullable|max:255',
            'am_weather_id' => 'required',
            'pm_business_type_id' => 'required',
            'pm_summary' => 'nullable|max:255',
            'pm_weather_id' => 'nullable',
            'remarks' => 'nullable|max:255',
        ]);

        DailyReport::create($request->all());

        return redirect()->route('master.daily-reports.index')->with('success', '日報を追加しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        $request->validate([
            'user_id' => 'required',
            'work_date' => 'required|date',
            'am_business_type_id' => 'required',
            'am_summary' => 'nullable|max:255',
            'am_weather_id' => 'required',
            'pm_business_type_id' => 'required',
            'pm_summary' => 'nullable|max:255',
            'pm_weather_id' => 'nullable',
            'remarks' => 'nullable|max:255',
        ]);

        $dailyReport->update($request->all());

        return redirect()->route('master.daily-reports.index')->with('success', '日報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReport $dailyReport)
    {
        $dailyReport->delete();

        return redirect()->route('master.daily-reports.index')->with('success', '日報を削除しました。');
    }
}
