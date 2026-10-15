<?php

namespace App\Policies;

use App\Models\Shared\DailyReport;
use App\Models\Master\User;

class DailyReportPolicy
{
    /**
     * 本人の日報かどうかを判定する。
     * view / update / delete いずれも「本人のみ」で共通のため1メソッドにまとめている。
     */
    public function view(User $user, DailyReport $dailyReport): bool
    {
        return $user->id === $dailyReport->user_id;
    }

    public function update(User $user, DailyReport $dailyReport): bool
    {
        return $user->id === $dailyReport->user_id;
    }

    public function delete(User $user, DailyReport $dailyReport): bool
    {
        return $user->id === $dailyReport->user_id;
    }
}
