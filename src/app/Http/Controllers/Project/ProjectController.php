<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Master\User;
use App\Models\Project\Project;
use App\Models\WorkerV2\WorkerGroupV2;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        /* ================================
         * 1. 横断キーワード検索
         * ================================ */
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('site_manager', 'like', "%{$keyword}%")
                    ->orWhere('site_alias', 'like', "%{$keyword}%")
                    ->orWhere('owner_name', 'like', "%{$keyword}%")
                    ->orWhere('owner_contact', 'like', "%{$keyword}%")
                    ->orWhere('location_address', 'like', "%{$keyword}%")
                    ->orWhere('location_subcompartment', 'like', "%{$keyword}%")
                    ->orWhere('project_name', 'like', "%{$keyword}%")
                    ->orWhere('work_content', 'like', "%{$keyword}%")
                    ->orWhere('note', 'like', "%{$keyword}%");
            });
        }

        /* ================================
         * 2. 選択式フィルタ
         * ================================ */
        $selectFields = [
            'staff_name',
            'order_type',
            'business_type',
        ];

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        foreach ($selectFields as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        /* ================================
         * 3. 期間検索（from ～ to）
         * ================================ */
        $ranges = [
            'ordered_on'   => ['ordered_from', 'ordered_to'],
            'started_on'   => ['started_from', 'started_to'],
            'completed_on' => ['completed_from', 'completed_to'],
        ];

        foreach ($ranges as $column => [$from, $to]) {
            if ($request->filled($from)) {
                $query->whereDate($column, '>=', $request->$from);
            }
            if ($request->filled($to)) {
                $query->whereDate($column, '<=', $request->$to);
            }
        }

        /* ================================
         * 4. ページネーション
         * ================================ */
        $projects = $query->orderBy('id', 'desc')->paginate(20);

        /* ================================
         * 5. 検索フォーム用データ
         * ================================ */

        // 担当者（users テーブル）
        $staffList = User::orderBy('name')->pluck('name');

        // 施工班（workergroups_v2 テーブル）
        $teams = WorkerGroupV2::active()
            ->orderBy('name')
            ->get();

        // 業務種類（projects から distinct）
        $businessTypes = Project::select('business_type')
            ->distinct()
            ->pluck('business_type');

        return view('projects.index', compact(
            'projects',
            'staffList',
            'teams',
            'businessTypes'
        ));
    }

    public function show(Project $project)
    {
        // 請求書も一緒に取得
        $project->load('invoices'); // 請求書もロード
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        $groups = WorkerGroupV2::active()
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('groups'), ['project' => new Project()]);
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            // 1. 親プロジェクト保存
            $project = Project::create($request->only([
                'order_no',
                'site_manager',
                'ordered_on',
                'staff_name',
                'site_alias',
                'owner_name',
                'owner_contact',
                'location_address',
                'location_subcompartment',
                'project_name',
                'work_content',
                'work_volume',
                'started_on',
                'completed_on',
                'machines',
                'work_cost',
                'notes',
                'attachments',
                'area_value',
                'area_unit',
                'volume_value',
                'volume_unit',
                'labor_units',
                'group_id',
                'note',
                'created_at',
                'updated_at',
                'order_type',
                'business_type',
                'is_protection_forest',
                'logging_notice_date',
                'subsidy_grant_date',
                'operation_history',
                'first_approver',
                'second_approver',
                'area_or_volume1',
                'area_or_volume2',
                'area_or_volume3',
                'unit_price',
                'expected_yield1',
                'expected_yield2',
                'personnel',
                'average_sales',
                'fiscal_year',
            ]));

            // 2. ratio 複数行保存
            if ($request->has('ratios')) {
                foreach ($request->ratios as $ratio) {
                    if (!empty($ratio['ratio_type']) && !empty($ratio['ratio'])) {
                        $project->ratios()->create([
                            'ratio_type' => $ratio['ratio_type'],
                            'ratio'      => $ratio['ratio'],
                        ]);
                    }
                }
            }

            // 3. destination 複数行保存
            if ($request->has('destinations')) {
                foreach ($request->destinations as $destination) {
                    if (!empty($destination['destination_type']) && !empty($destination['destination'])) {
                        $project->destinations()->create([
                            'destination_type' => $destination['destination_type'],
                            'destination'      => $destination['destination'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('projects.index')->with('success', '事業を登録しました');
    }

    public function edit(Project $project)
    {
        $groups = WorkerGroupV2::active()
            ->orderBy('name')
            ->get();

        return view('projects.edit', compact('project', 'groups'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            // 案件基本情報
            'order_type'    => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'owner_name'    => 'required|string|max:255',
            'site_alias'    => 'required|string|max:255',
            'started_on'    => 'nullable|date',
            'completed_on'  => 'nullable|date|after_or_equal:started_on',
            'area_value'    => 'nullable|numeric|min:0',
            'volume_value'  => 'nullable|numeric|min:0',
            'labor_units'   => 'nullable|numeric|min:0',
            'note'          => 'nullable|string',
            'fiscal_year'   => 'nullable|integer',

            // 新規追加項目（案件情報）
            'is_protection_forest' => 'nullable|in:有,無',
            'logging_notice_date'  => 'nullable|date',
            'operation_history'    => 'nullable|string|max:255',
            'first_approver'       => 'nullable|string|max:255',
            'second_approver'      => 'nullable|string|max:255',
            'subsidy_grant_date'   => 'nullable|date',

            // 作業指示書情報
            'ordered_on'              => 'nullable|date',
            'owner_contact'           => 'nullable|string|max:255',
            'site_manager'            => 'nullable|string|max:255',
            'staff_name'              => 'nullable|string|max:255',
            'location_address'        => 'nullable|string|max:255',
            'location_subcompartment' => 'nullable|string|max:255',
            'project_name'            => 'nullable|string|max:255',
            'work_content'            => 'nullable|string',
            'work_volume'             => 'nullable|string|max:255',
            'machines'                => 'nullable|string|max:255',
            'work_cost'               => 'nullable|string|max:255',
            'notes'                   => 'nullable|string',
            'attachments'             => 'nullable|string|max:255',

            // 新規追加項目（作業情報）
            'area_or_volume1'        => 'nullable|string|max:255',
            'workloaded_title1'      => 'nullable|string|max:255',
            'area_or_volume2'        => 'nullable|string|max:255',
            'workloaded_title2'      => 'nullable|string|max:255',
            'area_or_volume3'        => 'nullable|string|max:255',
            'workloaded_title3'      => 'nullable|string|max:255',
            'area_or_volume4'        => 'nullable|string|max:255',
            'workloaded_title4'      => 'nullable|string|max:255',
            'unit_price'             => 'nullable|string|max:255',
            'expected_yield1'        => 'nullable|numeric|min:0',
            'expected_yield2'        => 'nullable|numeric|min:0',
            'average_sales'          => 'nullable|numeric|min:0',

            'ratios.*.ratio_type'    => 'nullable|string|max:255',
            'ratios.*.ratio'         => 'nullable|numeric|min:0',

            'destinations.*.destination_type' => 'nullable|string|max:255',
            'destinations.*.destination'      => 'nullable|string|max:255',

            'area_unit'             => 'nullable|string|max:10',
            'volume_unit'           => 'nullable|string|max:10',
            'team_name'             => 'nullable|string|max:255',
            'group_id'              => 'nullable|integer',
            'personnel'             => 'nullable|integer'
        ]);

        DB::transaction(function () use ($project, $validated, $request) {
            $project->update($validated);

            if ($request->has('ratios')) {
                $project->ratios()->delete();

                foreach ($request->ratios as $ratio) {
                    if (!empty($ratio['ratio_type']) || !empty($ratio['ratio'])) {
                        $project->ratios()->create([
                            'ratio_type' => $ratio['ratio_type'] ?? null,
                            'ratio'      => $ratio['ratio'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->has('destinations')) {
                $project->destinations()->delete();

                foreach ($request->destinations as $destination) {
                    if (!empty($destination['destination_type']) || !empty($destination['destination'])) {
                        $project->destinations()->create([
                            'destination_type' => $destination['destination_type'] ?? null,
                            'destination'      => $destination['destination'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('projects.show', $project)
            ->with('success', '案件を更新しました');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index');
    }

    public function updateMetrics(Request $request, Project $project)
    {
        $validated = $request->validate([
            'area_value'   => 'nullable|numeric|min:0',
            'area_unit'    => 'nullable|string|max:50',
            'volume_value' => 'nullable|numeric|min:0',
            'volume_unit'  => 'nullable|string|max:50',
            'labor_units'  => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project->id)
            ->with('success', '面積・材積・人区を更新しました');
    }

    public function pdf(Project $project)
    {
        $project->load(['ratios', 'destinations']);
        $pdf = Pdf::loadView('projects.pdf', compact('project'));
        return $pdf->stream('project_' . $project->id . '.pdf');
    }

    public function pdfPreview(Request $request)
    {

        $fields = [
            'order_no',
            'site_manager',
            'ordered_on',
            'staff_name',
            'site_alias',
            'owner_name',
            'owner_contact',
            'location_address',
            'location_subcompartment',
            'project_name',
            'work_content',
            'work_volume',
            'started_on',
            'completed_on',
            'machines',
            'work_cost',
            'notes',
            'attachments',
            'area_value',
            'area_unit',
            'volume_value',
            'volume_unit',
            'labor_units',
            'team_name',
            'group_id',
            'fiscal_year',
            'note',
            'created_at',
            'updated_at',
            'order_type',
            'business_type',
            'is_protection_forest',
            'logging_notice_date',
            'subsidy_grant_date',
            'operation_history',
            'first_approver',
            'second_approver',
            'workloaded_title1',
            'area_or_volume1',
            'workloaded_title2',
            'area_or_volume2',
            'workloaded_title3',
            'area_or_volume3',
            'workloaded_title4',
            'area_or_volume4',
            'unit_price',
            'expected_yield1',
            'expected_yield2',
            'personnel',
            'average_sales',
        ];

        $project = new \stdClass();

        foreach ($fields as $f) {
            $project->$f = $request->$f ?? '';
        }

        // destinations は配列にしておく
        $project->destinations = $request->destinations ?? [];

        // 入力内容を仮の Project オブジェクトに変換
        $project = (object) $request->all();

        // PDF 生成
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projects.pdf', [
            'project' => $project
        ]);

        // Blob で返す（Content-Type: application/pdf）
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }


    public function orderNoCandidates(Request $request)
    {
        $groupId = $request->input('group_id');
        $fiscalYear = (int) $request->input('fiscal_year', date('Y')); // 選択年度 or 当年度

        // 選択した年度＋チームで最新の番号を取得
        $lastOrder = Project::where('group_id', $groupId)
            ->where('fiscal_year', $fiscalYear)
            ->orderBy('id', 'desc')
            ->first();

        $lastOrderNo = null;
        $nextNumber = 1;

        if ($lastOrder && $lastOrder->order_no) {
            $parts = explode('-', $lastOrder->order_no);
            if (count($parts) > 1) {
                $lastNumber = intval(end($parts));
                $nextNumber = $lastNumber + 1;
            }
            $lastOrderNo = $lastOrder->order_no;
        }

        $group = WorkerGroupV2::find($groupId);

        if (!$group) {
            return response()->json([
                'last_order_no' => null,
                'candidates'    => [],
            ]);
        }

        return response()->json([
            'last_order_no' => $lastOrderNo,
            'candidates'    => [
                $group->name . '-' . $nextNumber
            ],
        ]);
    }

    public function getActiveWorkerGroups()
    {
        $groups = WorkerGroupV2::active()
            ->orderBy('name')
            ->get();

        return response()->json($groups);
    }
}
