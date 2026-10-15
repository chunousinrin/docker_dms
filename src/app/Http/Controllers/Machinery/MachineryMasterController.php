<?php

namespace App\Http\Controllers\Machinery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Machinery\{
    MachineryMaster,
    MachineryCategory,
    MachineryAttachment
};

class MachineryMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machineries = MachineryMaster::with([
            'category',
            'attachments',
        ])
            ->orderBy('order_no')
            ->get();

        return view(
            'machinery.masters.index',
            compact('machineries')
        );
    }

    private function getFormData()
    {
        return [
            'categories' => MachineryCategory::orderBy('category_name')->get(),
            'attachments' => MachineryAttachment::orderBy('model_name')->get(),
        ];
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $machinery = new MachineryMaster();

        return view(
            'machinery.masters.create',
            array_merge(
                ['machinery' => $machinery],
                $this->getFormData()
            )
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:machinery_categories,id',
            'machine_name' => 'required|string|max:255',
            'status' => 'required',
            'ownership' => 'required',
            'manufacturer' => 'nullable|string|max:255',
            'base_machine' => 'nullable|string|max:255',
            'standard' => 'nullable|string|max:255',
            'introduction_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'current_hour_meter' => 'nullable|numeric',
            'manager_name' => 'nullable|string|max:255',
            'identification_number' => 'nullable|string|max:255',
            'next_maintenance_date' => 'nullable|date',
            'service_factory' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'order_no' => 'nullable|integer|min:0',
        ]);

        $machinery = MachineryMaster::create($validated);

        $machinery->attachments()->sync(
            $request->attachment_ids ?? []
        );

        return redirect()
            ->route('machinery-masters.show', $machinery)
            ->with('success', '重機を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(MachineryMaster $machinery_master)
    {
        $machinery_master->load([
            'category',
            'attachments',
            'maintenances' => function ($query) {
                $query->latest('effective_date');
            },
            'refuels' => function ($query) {
                $query->latest('refuel_date');
            },
            'usages' => function ($query) {
                $query->latest('start_date');
            },
        ]);

        return view(
            'machinery.masters.show',
            [
                'machinery' => $machinery_master,
            ]
        );
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MachineryMaster $machinery_master)
    {
        $machinery_master->load('attachments');

        return view(
            'machinery.masters.edit',
            array_merge(
                ['machinery' => $machinery_master],
                $this->getFormData()
            )
        );
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineryMaster $machinery_master)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:machinery_categories,id',
            'machine_name' => 'required|string|max:255',
            'status' => 'required',
            'ownership' => 'required',
            'manufacturer' => 'nullable|string|max:255',
            'base_machine' => 'nullable|string|max:255',
            'standard' => 'nullable|string|max:255',
            'introduction_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'current_hour_meter' => 'nullable|numeric',
            'manager_name' => 'nullable|string|max:255',
            'identification_number' => 'nullable|string|max:255',
            'next_maintenance_date' => 'nullable|date',
            'service_factory' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'order_no' => 'nullable|integer|min:0',
        ]);

        $machinery_master->update($validated);
        $machinery_master->attachments()->sync(
            $request->attachment_ids ?? []
        );
        return redirect()
            ->route('machinery-masters.show', $machinery_master)
            ->with('success', '重機情報を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineryMaster $machinery_master)
    {
        $machinery_master->delete();

        return redirect()
            ->route('machinery-masters.index')
            ->with('success', '重機を削除しました');
    }
}
