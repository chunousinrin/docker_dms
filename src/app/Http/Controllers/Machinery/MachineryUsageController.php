<?php

namespace App\Http\Controllers\Machinery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machinery\{
    MachineryMaster,
    MachineryUsage
};

class MachineryUsageController extends Controller
{
    public function create(MachineryMaster $machinery_master)
    {
        return view(
            'machinery.usages.create',
            compact('machinery_master')
        );
    }

    public function store(
        Request $request,
        MachineryMaster $machinery_master
    ) {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'work_place' => 'nullable|string|max:255',
            'instruction_number' => 'nullable|string|max:255',
            'worker_group_in_use' => 'nullable|string|max:255',
            'hour_meter_start' => 'nullable|numeric|min:0',
            'hour_meter_end' => 'nullable|numeric|gte:hour_meter_start',
            'remarks' => 'nullable|string',
        ]);

        $validated['machinery_master_id'] = $machinery_master->id;
        $validated['input_user_id'] = auth()->id();
        $validated['input_date'] = today();

        MachineryUsage::create($validated);

        if (
            $validated['hour_meter_end'] !== null &&
            (
                $machinery_master->current_hour_meter === null ||
                $validated['hour_meter_end'] > $machinery_master->current_hour_meter
            )
        ) {
            $machinery_master->update([
                'current_hour_meter' => $validated['hour_meter_end'],
            ]);
        }

        return redirect()
            ->route('machinery-masters.show', $machinery_master)
            ->with('success', '稼働履歴を登録しました');
    }
}
