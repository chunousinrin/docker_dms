<?php

namespace App\Http\Controllers\ForestPermit;

use App\Http\Controllers\Controller;
use App\Models\ForestPermit\ForestPermitApplication;
use Illuminate\Http\Request;

class ForestPermitApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $applications = (clone $query)
            ->orderByRaw('permit_end_date IS NULL')
            ->orderByDesc('permit_end_date')
            ->paginate(20)
            ->withQueryString();

        $today = today();
        $warningLimit = $today->copy()->addDays(30);

        $baseQuery = (clone $query)
            ->whereNull('completion_date')
            ->whereNotNull('permit_end_date');

        $expiredCount = (clone $baseQuery)
            ->whereDate('permit_end_date', '<', $today)
            ->count();

        $warningCount = (clone $baseQuery)
            ->whereDate('permit_end_date', '>=', $today)
            ->whereDate('permit_end_date', '<=', $warningLimit)
            ->count();

        $activeCount = (clone $baseQuery)
            ->whereDate('permit_end_date', '>', $warningLimit)
            ->count();

        return view(
            'forest_permit.index',
            compact('applications', 'expiredCount', 'warningCount', 'activeCount')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forest_permit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'management_no' => ['required', 'integer'],
            'branch_no' => ['required', 'integer'],
            'facility_name' => ['nullable', 'string', 'max:255'],
            'protection_forest_type' => ['nullable', 'string', 'max:50'],
            'forest_location' => ['nullable', 'string', 'max:255'],
            'parcel_count' => ['nullable', 'numeric'],
            'applicant_name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'permit_area' => ['nullable', 'numeric'],
            'application_date' => ['nullable', 'date'],
            'permit_date' => ['nullable', 'date'],
            'order_no' => ['nullable', 'string', 'max:50'],
            'permit_start_date' => ['nullable', 'date'],
            'permit_end_date' => ['nullable', 'date'],
            'clear_cut_completion_date' => ['nullable', 'date'],
            'planting_completion_date' => ['nullable', 'date'],
            'submission_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        ForestPermitApplication::create($validated);

        return redirect()
            ->route('forest-permit.index')
            ->with('success', '許認可情報を登録しました。');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ForestPermitApplication $forest_permit)
    {
        $application = $forest_permit;

        return view(
            'forest_permit.edit',
            compact('application')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        ForestPermitApplication $forestPermit
    ) {
        $validated = $request->validate([
            'management_no' => ['required', 'integer'],
            'branch_no' => ['required', 'integer'],
            'facility_name' => ['nullable', 'string', 'max:255'],
            'protection_forest_type' => ['nullable', 'string', 'max:50'],
            'forest_location' => ['nullable', 'string', 'max:255'],
            'parcel_count' => ['nullable', 'numeric'],
            'applicant_name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'permit_area' => ['nullable', 'numeric'],
            'application_date' => ['nullable', 'date'],
            'permit_date' => ['nullable', 'date'],
            'order_no' => ['nullable', 'string', 'max:50'],
            'permit_start_date' => ['nullable', 'date'],
            'permit_end_date' => ['nullable', 'date'],
            'clear_cut_completion_date' => ['nullable', 'date'],
            'planting_completion_date' => ['nullable', 'date'],
            'submission_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $forestPermit->update($validated);

        return redirect()
            ->route('forest-permit.index')
            ->with('success', '許認可情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ForestPermitApplication $forestPermit)
    {
        $forestPermit->delete();

        return redirect()
            ->route('forest-permit.index')
            ->with('success', '許認可情報を削除しました。');
    }

    private function buildQuery(Request $request)
    {
        $query = ForestPermitApplication::query();

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('facility_name', 'like', "%{$keyword}%")
                    ->orWhere('forest_location', 'like', "%{$keyword}%")
                    ->orWhere('applicant_name', 'like', "%{$keyword}%")
                    ->orWhere('order_no', 'like', "%{$keyword}%")
                    ->orWhere('protection_forest_type', 'like', "%{$keyword}%")
                    ->orWhere('remarks', 'like', "%{$keyword}%")
                    ->orWhere('contact', 'like', "%{$keyword}%")
                    ->orWhereRaw(
                        "CONCAT(management_no,'-',branch_no) LIKE ?",
                        ["%{$keyword}%"]
                    );

                if (is_numeric($keyword)) {
                    $q->orWhere('management_no', $keyword);
                }
            });
        }

        if ($request->filled('protection_forest_type')) {
            $query->where(
                'protection_forest_type',
                'like',
                '%' . $request->protection_forest_type . '%'
            );
        }

        if ($request->filled('application_date_from')) {
            $query->whereDate(
                'application_date',
                '>=',
                $request->application_date_from
            );
        }

        if ($request->filled('application_date_to')) {
            $query->whereDate(
                'application_date',
                '<=',
                $request->application_date_to
            );
        }

        if ($request->filled('permit_date_from')) {
            $query->whereDate(
                'permit_date',
                '>=',
                $request->permit_date_from
            );
        }

        if ($request->filled('permit_date_to')) {
            $query->whereDate(
                'permit_date',
                '<=',
                $request->permit_date_to
            );
        }


        if ($request->filled('status')) {
            $query->whereNull('completion_date');
            $today = today();
            $warningLimit = $today->copy()->addDays(30);

            switch ($request->status) {
                case 'expired':
                    $query->whereNotNull('permit_end_date')
                        ->whereDate('permit_end_date', '<', $today);
                    break;

                case 'warning':
                    $query->whereNotNull('permit_end_date')
                        ->whereDate('permit_end_date', '>=', $today)
                        ->whereDate('permit_end_date', '<=', $warningLimit);
                    break;

                case 'active':
                    $query->whereNotNull('permit_end_date')
                        ->whereDate('permit_end_date', '>', $warningLimit);
                    break;
            }
        }

        return $query;
    }

    public function exportCsv(Request $request)
    {
        $applications = $this->buildQuery($request)
            ->orderByDesc('management_no')
            ->orderBy('branch_no')
            ->get();

        $filename = sprintf(
            '許認可申請一覧_%s.csv',
            now()->format('Ymd_His')
        );
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($applications) {

            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '整理番号',
                '施設名称',
                '保安林種',
                '申請者',
                '許可面積',
                '申請年月日',
                '許可年月日',
                '指令番号',
                '許可始期',
                '許可終期',
                '残日数',
                '状態',
                '着手日',
                '完了日',
                '備考',
            ]);

            foreach ($applications as $row) {

                fputcsv($handle, [
                    $row->display_no,
                    $row->facility_name,
                    $row->protection_forest_type,
                    $row->applicant_name,
                    $row->permit_area,
                    $row->application_date?->format('Y/m/d'),
                    $row->permit_date?->format('Y/m/d'),
                    $row->order_no,
                    $row->permit_start_date?->format('Y/m/d'),
                    $row->permit_end_date?->format('Y/m/d'),
                    $row->remaining_days,
                    $row->permit_status,
                    $row->start_date?->format('Y/m/d'),
                    $row->completion_date?->format('Y/m/d'),
                    $row->remarks,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
