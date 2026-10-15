<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\DailyReportWeather;
use Illuminate\Http\Request;

class DailyReportWeatherController extends Controller
{
    public function index()
    {
        $weathers = DailyReportWeather::orderBy('id')->paginate(20);

        return view('daily_report_weathers.index', compact('weathers'));
    }

    public function create()
    {
        return view('daily_report_weathers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            ['name' => ['required', 'string', 'max:255', 'unique:daily_report_weathers,name']],
            [],
            ['name' => '天気名']
        );

        DailyReportWeather::create($validated);

        return redirect()
            ->route('daily-report-weathers.index')
            ->with('success', '天気を登録しました。');
    }

    public function edit(DailyReportWeather $dailyReportWeather)
    {
        return view('daily_report_weathers.edit', compact('dailyReportWeather'));
    }

    public function update(Request $request, DailyReportWeather $dailyReportWeather)
    {
        $validated = $request->validate(
            ['name' => ['required', 'string', 'max:255', 'unique:daily_report_weathers,name,' . $dailyReportWeather->id]],
            [],
            ['name' => '天気名']
        );

        $dailyReportWeather->update($validated);

        return redirect()
            ->route('daily-report-weathers.index')
            ->with('success', '天気を更新しました。');
    }

    public function destroy(DailyReportWeather $dailyReportWeather)
    {
        $dailyReportWeather->delete();

        return redirect()
            ->route('daily-report-weathers.index')
            ->with('success', '天気を削除しました。');
    }
}
