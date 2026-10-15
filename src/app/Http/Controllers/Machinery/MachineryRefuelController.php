<?php

namespace App\Http\Controllers\Machinery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machinery\{
    MachineryMaster,
    MachineryRefuel
};


class MachineryRefuelController extends Controller
{
    public function create(MachineryMaster $machinery_master)
    {
        return view(
            'machinery.refuels.create',
            compact('machinery_master')
        );
    }

    public function store(Request $request, MachineryMaster $machinery_master)
    {
        $validated = $request->validate([
            'refuel_date' => 'required|date',
            'fuel_volume' => 'required|numeric|min:0',
            'gas_station' => 'nullable|string|max:255',
            'fuel_location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['machinery_master_id'] = $machinery_master->id;
        $validated['input_user_id'] = auth()->id();
        $validated['input_date'] = now()->toDateString();

        MachineryRefuel::create($validated);

        return redirect()
            ->route('machinery-masters.show', $machinery_master)
            ->with('success', '給油記録を登録しました');
    }

    public function destroy(MachineryRefuel $refuel)
    {
        $machineryMasterId = $refuel->machinery_master_id;

        $refuel->delete();

        return redirect()
            ->route('machinery-masters.show', $machineryMasterId)
            ->with('success', '給油記録を削除しました');
    }
}
