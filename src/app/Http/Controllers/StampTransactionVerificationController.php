<?php

namespace App\Http\Controllers;

use App\Models\StampTransaction;
use App\Models\StampTransactionVerification as Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * 入出庫履歴の第三者確認。
 * 記録者以外の人が、画面上の履歴と実物を突き合わせて確認記録を残す。
 * routes 側で can:verify-transactions を付与する。
 */
class StampTransactionVerificationController extends Controller
{
    public function create(StampTransaction $transaction)
    {
        if ($redirect = $this->guard($transaction)) {
            return $redirect;
        }

        $transaction->load(['item', 'user']);

        return view('stamps.transactions.verify', compact('transaction'));
    }

    public function store(Request $request, StampTransaction $transaction)
    {
        if ($redirect = $this->guard($transaction)) {
            return $redirect;
        }

        $data = $request->validate([
            'actual_qty' => ['required', 'integer', 'min:0', 'max:1000000'],
            'memo'       => ['nullable', 'string', 'max:255'],
        ]);

        $expected = abs($transaction->quantity);
        $actual   = (int) $data['actual_qty'];
        $status   = $actual === $expected
            ? Verification::STATUS_CONFIRMED
            : Verification::STATUS_DISCREPANCY;

        if ($status === Verification::STATUS_DISCREPANCY && blank($data['memo'] ?? null)) {
            throw ValidationException::withMessages([
                'memo' => "記録（{$expected}）と実物（{$actual}）が一致しません。差異の内容をメモに入力してください。",
            ]);
        }

        DB::transaction(function () use ($transaction, $expected, $actual, $status, $data) {
            // 同時に二重確認されないよう履歴行をロック
            $locked = StampTransaction::lockForUpdate()->findOrFail($transaction->id);

            if ($locked->verification()->exists()) {
                throw ValidationException::withMessages(['actual_qty' => 'この履歴はすでに確認済みです。']);
            }

            Verification::create([
                'stamp_transaction_id' => $locked->id,
                'verifier_id'          => auth()->id(),
                'status'               => $status,
                'expected_qty'         => $expected,
                'actual_qty'           => $actual,
                'memo'                 => $data['memo'] ?? null,
                'verified_at'          => now(),
            ]);
        });

        $message = $status === Verification::STATUS_CONFIRMED
            ? '確認を記録しました'
            : '差異ありとして記録しました。管理者に連絡してください。';

        return redirect()->route('stamps.transactions.index', ['verification' => 'unverified'])
            ->with($status === Verification::STATUS_CONFIRMED ? 'success' : 'warning', $message);
    }

    /** 確認できない場合の戻り先。問題なければ null。 */
    private function guard(StampTransaction $transaction)
    {
        $transaction->loadMissing('verification');

        if (! $transaction->isVerifiable()) {
            abort(404);
        }
        if ($transaction->verification) {
            return redirect()->route('stamps.transactions.index')
                ->withErrors(['verify' => 'この履歴はすでに確認済みです。']);
        }
        if (! $transaction->canBeVerifiedBy(auth()->user())) {
            abort(403, '自分が記録した履歴は確認できません。別の方に確認を依頼してください。');
        }

        return null;
    }
}
