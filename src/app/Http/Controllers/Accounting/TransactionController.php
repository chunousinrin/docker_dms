<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Storage,
    DB
};

use App\Models\Master\Customer;
use App\Models\Accounting\{
    Transaction,
    TransactionFile
};

use App\Services\TransactionStorageService;

class TransactionController extends Controller
{
    protected $storageService;

    public function __construct(TransactionStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * 一覧・検索表示
     */
    public function index(Request $request)
    {
        // 電帳法の検索3要件に対応
        $query = Transaction::with('files');

        // 1. 取引年月日の範囲検索
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        // 2. 取引金額の範囲検索
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // 3. 取引先名での検索
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', "%{$request->customer_name}%");
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * 保存処理
     */
    public function store(Request $request)
    {
        // バリデーション（customer_id ではなく customer_name を必須にする）
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric',
            'note'             => 'nullable|string',
            'documents'        => 'required|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,png',
            'documents.*.type' => 'required|string',
        ]);

        // 【重要】取引先がなければ新しく作り、IDを取得する
        $customer = Customer::firstOrCreate(
            ['name' => $request->customer_name]
        );

        // 既存のバリデーション済みデータに customer_id を合流させる
        $transactionData = array_merge($validated, [
            'customer_id' => $customer->id,
            'user_id'     => auth()->id(),
        ]);

        // Serviceを呼び出して保存
        $this->storageService->store($transactionData, $request->documents);

        return redirect()->route('transactions.index')->with('success', 'データを保存しました。');
    }

    public function create()
    {
        return view('transactions.create');
    }

    public function show(Transaction $transaction)
    {
        // Eager Loadingで紐づくファイルを一気に取得
        $transaction->load('files');
        return view('transactions.show', compact('transaction'));
    }


    public function download(TransactionFile $file)
    {
        // セキュリティチェック（例：ログイン中か、閲覧権限があるか等）をここで実施

        if (!Storage::disk('transaction')->exists($file->file_path)) {
            abort(404, 'ファイルが見つかりません。');
        }

        // ブラウザで表示させる（inline）か、ダウンロードさせる（attachment）か選べます
        // 電帳法の確認用なら inline が便利です
        return Storage::disk('transaction')->response($file->file_path);
    }
    public function destroy(Transaction $transaction)
    {
        try {
            DB::transaction(function () use ($transaction) {
                // 1. 紐づく物理ファイルをすべて削除
                foreach ($transaction->files as $file) {
                    if (Storage::disk('transaction')->exists($file->file_path)) {
                        Storage::disk('transaction')->delete($file->file_path);
                    }
                }

                // 2. DBから子レコード（files）と親レコード（transaction）を削除
                // ※リレーションで onDelete('cascade') を設定していない場合を想定
                $transaction->files()->delete();
                $transaction->delete();
            });

            return redirect()->route('transactions.index')->with('success', 'データを削除しました。');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', '削除に失敗しました。');
        }
    }
    // App\Http\Controllers\TransactionController.php

}
