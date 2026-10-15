<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function edit()
    {
        $branches = Branch::orderBy('id')->get();

        return view('master.company.edit', compact('branches'));
    }

    public function update(Request $request)
    {
        foreach ($request->branches as $id => $data) {

            Branch::where('id', $id)->update([
                'name'           => $data['name'],
                'representative' => $data['representative'],
                'postal_code'    => $data['postal_code'],
                'address'        => $data['address'],
                'phone'          => $data['phone'],
                'fax'            => $data['fax'],
            ]);
        }

        return redirect()
            ->route('master.company.edit')
            ->with('success', '会社情報を更新しました');
    }
}
