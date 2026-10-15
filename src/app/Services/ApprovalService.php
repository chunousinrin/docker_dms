<?php

namespace App\Services;

use App\Models\Request\RingiRequest;
use App\Models\Request\ApprovalStep;
use App\Models\Request\ApprovalHistory;
use App\Models\Request\Attachment;
use Illuminate\Support\Facades\DB;
use Exception;

class ApprovalService
{
    /**
     * 承認
     */
    public function approve(int $stepId, int $userId, ?string $comment = null)
    {
        return DB::transaction(function () use ($stepId, $userId, $comment) {

            $step = ApprovalStep::with('request')->findOrFail($stepId);
            $request = $step->request;

            // ① 自分の承認かチェック
            if ($step->approver_id !== $userId) {
                throw new Exception('承認権限がありません');
            }

            // ② ステータスチェック
            if ($step->status !== 'pending') {
                throw new Exception('すでに処理済みです');
            }

            // ③ 現在ステップかチェック
            if ($step->step_order !== $request->current_step) {
                throw new Exception('あなたの承認順ではありません');
            }

            // ④ 現在のファイルバージョン取得
            $currentVersion = Attachment::where('request_id', $request->id)
                ->max('version');

            // ⑤ 承認
            $step->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // ⑥ 履歴保存
            ApprovalHistory::create([
                'request_id' => $request->id,
                'step_id' => $step->id,
                'action' => 'approved',
                'comment' => $comment,
                'approved_by' => $userId,
                'approved_at' => now(),
                'file_version_at_approval' => $currentVersion,
            ]);

            // ⑦ 次ステップ確認
            $nextStep = ApprovalStep::where('request_id', $request->id)
                ->where('step_order', $request->current_step + 1)
                ->first();

            if ($nextStep) {
                // 次へ進む
                $request->increment('current_step');
            } else {
                // 全承認完了
                $request->update([
                    'status' => 'approved'
                ]);
            }

            return true;
        });
    }

    /**
     * 却下
     */
    public function reject(int $stepId, int $userId, ?string $comment = null)
    {
        return DB::transaction(function () use ($stepId, $userId, $comment) {

            $step = ApprovalStep::with('request')->findOrFail($stepId);
            $request = $step->request;

            // 権限チェック
            if ($step->approver_id !== $userId) {
                throw new Exception('権限がありません');
            }

            if ($step->status !== 'pending') {
                throw new Exception('すでに処理済みです');
            }

            if ($step->step_order !== $request->current_step) {
                throw new Exception('あなたの承認順ではありません');
            }

            // ファイルバージョン
            $currentVersion = Attachment::where('request_id', $request->id)
                ->max('version');

            // ステップ更新
            $step->update([
                'status' => 'rejected',
                'approved_at' => now(),
            ]);

            // 履歴
            ApprovalHistory::create([
                'request_id' => $request->id,
                'step_id' => $step->id,
                'action' => 'rejected',
                'comment' => $comment,
                'approved_by' => $userId,
                'approved_at' => now(),
                'file_version_at_approval' => $currentVersion,
            ]);

            // 稟議書を却下
            $request->update([
                'status' => 'rejected'
            ]);

            return true;
        });
    }
}
