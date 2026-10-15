<?php

namespace App\Http\Controllers\Master;

use App\Models\Master\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::ordered()->withCount(['members', 'sections'])->get();
        return view('master.districts.index', compact('districts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:districts,code',
            'sort_order' => 'nullable|integer',
        ]);

        District::create($data);

        return back()->with('success', '地区を登録しました。');
    }

    public function update(Request $request, District $district)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:districts,code,' . $district->id,
            'sort_order' => 'nullable|integer',
        ]);

        $district->update($data);

        return back()->with('success', '地区を更新しました。');
    }

    public function destroy(District $district)
    {
        if ($district->members()->exists()) {
            return back()->with('error', 'この地区に所属する組合員がいるため削除できません。');
        }

        $district->delete();

        return back()->with('success', '地区を削除しました。');
    }
}
