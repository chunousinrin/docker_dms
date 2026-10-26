<?php

namespace App\Policies;

use App\Models\Accounting\Transaction;
use App\Models\Master\User;

/**
 * 暫定ルール：
 *  - 閲覧: ログイン済みの全員
 *  - 取消: 登録者本人、または管理者
 * 自社の権限モデル（ロール/権限）に合わせて isAdmin() を差し替えてください。
 */
class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return true;
    }

    public function cancel(User $user, Transaction $transaction): bool
    {
        if ($transaction->trashed()) {
            return false;
        }

        return $user->id === $transaction->user_id || $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return (bool) ($user->is_admin ?? false);
    }
}
