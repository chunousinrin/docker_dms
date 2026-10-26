<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreTransactionRequest;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionAuditLog;
use App\Models\Accounting\TransactionFile;
use App\Models\Master\Customer;
use App\Services\TransactionStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function __construct(private TransactionStorageService $storageService) {}

    /**
     * 一覧・検索（電帳法の検索3要件：日付範囲・金額範囲・取引先、およびその組合せ）
     */
    public function index(Request $request)
    {
        $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'date_from'     => ['nullable', 'date'],
            'date_to'       => ['nullable', 'date', $request->filled('date_from') ? 'after_or_equal:date_from' : 'date'],
            'amount_min'    => ['nullable', 'integer'],
            'amount_max'    => ['nullable', 'integer', $request->filled('amount_min') ? 'gte:amount_min' : 'integer'],
            'include_void'  => ['nullable', 'boolean'],
        ]);

        $query = Transaction::with(['files', 'user']);

        // 取消済みを含めるか（既定は含めない。含めた場合も記録は検索・閲覧可能）
        if ($request->boolean('include_void')) {
            $query->withTrashed();
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }
        if ($request->filled('customer_name')) {
            // LIKE のワイルドカード(% _ \)をエスケープして文字どおり検索する
            $keyword = addcslashes($request->customer_name, '%_\\');
            $query->where('customer_name', 'like', "%{$keyword}%");
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString(); // ページ送りで検索条件を維持

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('transactions.create', [
            'customers' => Customer::orderBy('name')->pluck('name'),
            'fileTypes' => TransactionFile::TYPES,
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->storageService->store(
            $request->safe()->except('documents') + ['user_id' => auth()->id()],
            $request->validated('documents'),
        );

        // 登録画面は fancybox の iframe 内で開かれるため、一旦中継ページ経由で親画面を遷移させる
        return redirect()->route('transactions.saved')->with('success', 'データを保存しました。');
    }

    /**
     * iframe を抜けて一覧に戻る中継ページ
     */
    public function saved(Request $request)
    {
        $request->session()->reflash(); // フラッシュメッセージを一覧画面まで持ち越す
        return view('transactions.saved');
    }

    public function show(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['files', 'user', 'deleter']);
        $logs = $transaction->auditLogs()->with('user')->latest('id')->limit(50)->get();

        return view('transactions.show', compact('transaction', 'logs'));
    }

    /**
     * 証憑ファイルの表示（inline）
     */
    public function download(TransactionFile $file)
    {
        Gate::authorize('view', $file->transaction);

        $disk = Storage::disk('transaction');

        if (! $disk->exists($file->file_path)) {
            TransactionAuditLog::record('integrity_error', null, $file, null, ['reason' => 'file_missing']);
            abort(404, 'ファイルが見つかりません。');
        }

        // 表示のたびにハッシュを照合し、改ざん・破損を検知する
        $actual = hash_file('sha256', $disk->path($file->file_path));
        if ($file->file_hash && ! hash_equals($file->file_hash, $actual)) {
            TransactionAuditLog::record('integrity_error', null, $file, ['sha256' => $file->file_hash], ['sha256' => $actual]);
            abort(500, 'ファイルの整合性チェックに失敗しました。管理者に連絡してください。');
        }

        TransactionAuditLog::record('file_viewed', null, $file);

        return $disk->response(
            $file->file_path,
            $file->original_filename,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }

    /**
     * 取消（物理削除はしない）。証憑ファイルと履歴は法定保存期間のあいだ残す。
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        Gate::authorize('cancel', $transaction);

        $validated = $request->validate([
            'void_reason' => ['required', 'string', 'max:500'],
        ], [], ['void_reason' => '取消理由']);

        $this->storageService->voidTransaction($transaction, $validated['void_reason'], auth()->id());

        return redirect()->route('transactions.index')->with('success', '取引を取消しました（証憑ファイルと履歴は保存されています）。');
    }
}
