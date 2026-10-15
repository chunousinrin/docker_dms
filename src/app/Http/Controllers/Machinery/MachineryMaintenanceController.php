<?php

namespace App\Http\Controllers\Machinery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Machinery\{
    MachineryMaster,
    MachineryMaintenance
};

class MachineryMaintenanceController extends Controller
{
    public function create(MachineryMaster $machinery_master)
    {
        return view(
            'machinery.maintenances.create',
            compact('machinery_master')
        );
    }

    public function store(
        Request $request,
        MachineryMaster $machinery_master
    ) {
        $validated = $request->validate([
            'effective_date' => 'required|date',
            'maintenance_details' => 'required|string',
            'cost' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        $validated['machinery_master_id'] = $machinery_master->id;

        $validated['input_user_id'] = auth()->id();

        $validated['input_date'] = today();

        MachineryMaintenance::create($validated);

        return redirect()
            ->route('machinery-masters.show', $machinery_master)
            ->with('success', 'メンテナンス履歴を登録しました');
    }

    public function destroy(MachineryMaintenance $maintenance)
    {
        $machinery = $maintenance->machineryMaster;

        $maintenance->delete();

        return redirect()
            ->route('machinery-masters.show', $machinery)
            ->with('success', '整備履歴を削除しました');
    }
}
