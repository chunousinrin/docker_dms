<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;

use App\Models\Project\ProjectCost;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    public function store(Request $request)
    {
        // バリデーションルール
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category'   => 'required|string|max:50',
            'amount'     => 'required|integer|min:0',
        ]);

        // 保存処理
        ProjectCost::create($validated);

        // 案件詳細へリダイレクト
        return redirect()->route('projects.show', $validated['project_id'])
            ->with('success', '費用を追加しました');
    }

    // 費用更新
    public function update(Request $request, ProjectCost $projectCost)
    {
        // バリデーションルール
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'amount'   => 'required|integer|min:0',
        ]);

        // 更新処理
        $projectCost->update($validated);

        // 案件詳細へリダイレクト
        return redirect()->route('projects.show', $projectCost->project_id)
            ->with('success', '費用を更新しました');
    }

    // 費用削除
    public function destroy(ProjectCost $projectCost)
    {
        $projectId = $projectCost->project_id;

        // 削除処理
        $projectCost->delete();

        // 案件詳細へリダイレクト
        return redirect()->route('projects.show', $projectId)
            ->with('success', '費用を削除しました');
    }
}
