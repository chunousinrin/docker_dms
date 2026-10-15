<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forest\ForestRecord;
use Illuminate\Support\Facades\Schema;


class SinrinboController extends Controller
{
    public function sinrinbo()
    {
        return view('sinrinbo.sinrinbo');
    }
    public function sinrinbo_export()
    {
        return view('sinrinbo.sinrinbo_export');
    }
}
