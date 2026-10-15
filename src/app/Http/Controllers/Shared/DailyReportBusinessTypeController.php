<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\DailyReportBusinessType;
use Illuminate\Http\Request;

class DailyReportBusinessTypeController extends Controller
{
    public function index()
    {
        $businessTypes = DailyReportBusinessType::orderBy('name')->paginate(20);

        return view('daily_report_business_types.index', compact('businessTypes'));
    }

    public function create()
    {
        return view('daily_report_business_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            ['name' => ['required', 'string', 'max:255', 'unique:daily_report_business_types,name']],
            [],
            ['name' => '業種名']
        );

        DailyReportBusinessType::create($validated);

        return redirect()
            ->route('daily-report-business-types.index')
            ->with('success', '業種を登録しました。');
    }

    public function edit(DailyReportBusinessType $dailyReportBusinessType)
    {
        return view('daily_report_business_types.edit', compact('dailyReportBusinessType'));
    }

    public function update(Request $request, DailyReportBusinessType $dailyReportBusinessType)
    {
        $validated = $request->validate(
            ['name' => ['required', 'string', 'max:255', 'unique:daily_report_business_types,name,' . $dailyReportBusinessType->id]],
            [],
            ['name' => '業種名']
        );

        $dailyReportBusinessType->update($validated);

        return redirect()
            ->route('daily-report-business-types.index')
            ->with('success', '業種を更新しました。');
    }

    public function destroy(DailyReportBusinessType $dailyReportBusinessType)
    {
        $dailyReportBusinessType->delete();

        return redirect()
            ->route('daily-report-business-types.index')
            ->with('success', '業種を削除しました。');
    }
}
