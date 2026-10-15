<?php

namespace App\Policies;

use App\Models\Master\Member;
use App\Models\Master\User;

class MemberPolicy
{
    /**
     * 閲覧は全ユーザー可能。
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Member $member): bool
    {
        return true;
    }

    /**
     * 新規登録・編集・更新・削除は auth = 0 のユーザーのみ許可。
     * 出資増減／山林面積増減／出資預り書／広報誌発送履歴の登録・削除も
     * 「組合員情報を更新できるか」と同じ基準でよいため、これらのコントローラーからも
     * この update 権限をそのまま流用します。
     */
    public function create(User $user): bool
    {
        return $this->canEdit($user);
    }

    public function update(User $user, Member $member): bool
    {
        return $this->canEdit($user);
    }

    public function delete(User $user, Member $member): bool
    {
        return $this->canEdit($user);
    }

    private function canEdit(User $user): bool
    {
        return (int) $user->auth_type === 0;
    }
}
