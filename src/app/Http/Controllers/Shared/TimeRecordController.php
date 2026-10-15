<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Master\User;
use App\Models\Shared\TimeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeRecordController extends Controller
{
    public function userIndex()
    {
        $users = User::where('used', 1)->orderBy('id')->get();

        return view('time_records.users', compact('users'));
    }

    public function show(User $user)
    {
        $today = Carbon::today()->toDateString();

        $timeRecord = TimeRecord::firstOrNew([
            'user_id'   => $user->id,
            'work_date' => $today,
        ]);

        return view('time_records.show', compact('user', 'timeRecord'));
    }

    public function punch(Request $request, User $user)
    {
        $request->validate([
            'type' => 'required|in:start_time,break_start_time,break_end_time,end_time',
        ]);

        $today = Carbon::today()->toDateString();

        $timeRecord = TimeRecord::firstOrCreate([
            'user_id'   => $user->id,
            'work_date' => $today,
        ]);

        $field = $request->input('type');

        // サーバー側でも状態チェック
        if (! $timeRecord->canPunch($field)) {
            return response()->json([
                'success' => false,
                'message' => 'この操作は現在実行できません。画面を更新してください。',
            ], 422);
        }

        $now = Carbon::now();
        $timeRecord->{$field} = $now->format('H:i:s');
        $timeRecord->save();

        return response()->json([
            'success'       => true,
            'type'          => $field,
            'time'          => $now->format('H:i'),
            'allowed_types' => $timeRecord->allowedPunchTypes(), // 次に押せる項目を返す
        ]);
    }

    public function saveNote(Request $request, User $user)
    {
        $today = Carbon::today()->toDateString();

        $timeRecord = TimeRecord::firstOrCreate([
            'user_id'   => $user->id,
            'work_date' => $today,
        ]);

        $timeRecord->note = $request->input('note');
        $timeRecord->save();

        return response()->json(['success' => true]);
    }
}
