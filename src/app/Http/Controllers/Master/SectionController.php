<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Master\District;
use App\Models\Master\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('district')->orderBy('district_id')->ordered()->get();
        $districts = District::ordered()->get();
        return view('master.sections.index', compact('sections', 'districts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        Section::create($data);

        return back()->with('success', '字を登録しました。');
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        $section->update($data);

        return back()->with('success', '字を更新しました。');
    }

    public function destroy(Section $section)
    {
        if ($section->members()->exists()) {
            return back()->with('error', 'この字に所属する組合員がいるため削除できません。');
        }

        $section->delete();

        return back()->with('success', '字を削除しました。');
    }

    /**
     * 組合員フォームで「地区」選択時に、その地区に属する「字」一覧を返すAJAX API
     * GET /api/districts/{district}/sections
     */
    public function byDistrict(District $district)
    {
        return response()->json(
            $district->sections()->ordered()->get(['id', 'name'])
        );
    }
}
