<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #fff;
            padding: 20px;
            font-size: 0.9rem;
        }

        .table-th {
            background: #f8faf8;
            color: var(--main-color);
            font-weight: 700;
            width: 30%;
        }
    </style>
</head>

<body>

    <div class="pb-2 mb-3 border-bottom">
        <h5 class="m-0 font-weight-bold" style="color: var(--main-color);">
            <i class="fas fa-file-invoice mr-2"></i> 取引詳細確認
            @if($transaction->trashed())
            <span class="badge badge-secondary ml-2">取消済</span>
            @endif
        </h5>
    </div>

    @if($transaction->trashed())
    <div class="alert alert-secondary small py-2">
        {{ $transaction->deleted_at->format('Y-m-d H:i') }} に {{ $transaction->deleter->name ?? '不明' }} が取消しました。<br>
        理由: {{ $transaction->void_reason ?? '-' }}
    </div>
    @endif

    <table class="table table-bordered table-sm">
        <tr>
            <th class="table-th">取引年月日</th>
            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <th class="table-th">取引先</th>
            <td class="font-weight-bold">{{ $transaction->customer_name }}</td>
        </tr>
        <tr>
            <th class="table-th">金額</th>
            <td>￥{{ number_format($transaction->amount) }}</td>
        </tr>
        <tr>
            <th class="table-th">登録者</th>
            <td>{{ $transaction->user->name ?? '不明' }}</td>
        </tr>
        <tr>
            <th class="table-th">登録日時</th>
            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <th class="table-th">備考</th>
            <td>{{ $transaction->note ?? '-' }}</td>
        </tr>
    </table>

    <h5 class="mt-4 mb-2 h6 font-weight-bold" style="color: var(--main-color);">
        <i class="fas fa-file-pdf mr-1"></i> 証跡書類
    </h5>
    <div class="list-group">
        @forelse($transaction->files as $file)
        <a href="{{ route('transaction-files.view', $file) }}" target="_blank" rel="noopener" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
            <div>
                <span class="badge badge-info mr-2">{{ $file->file_type }}</span>
                {{ $file->original_filename }}
                <div class="text-muted" style="font-size: 0.7rem;">
                    SHA-256: {{ \Illuminate\Support\Str::limit($file->file_hash, 16, '…') }}
                    @if($file->file_size) ／ {{ number_format($file->file_size / 1024, 1) }} KB @endif
                </div>
            </div>
            <i class="fas fa-external-link-alt text-primary" style="font-size: 0.8rem;"></i>
        </a>
        @empty
        <p class="text-muted small pl-1">添付ファイルはありません</p>
        @endforelse
    </div>

    <h5 class="mt-4 mb-2 h6 font-weight-bold" style="color: var(--main-color);">
        <i class="fas fa-history mr-1"></i> 操作履歴（直近50件）
    </h5>
    <table class="table table-sm table-bordered small">
        <thead>
            <tr>
                <th style="width: 150px;">日時</th>
                <th style="width: 110px;">操作</th>
                <th>実行者</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr @class(['table-danger' => $log->action === 'integrity_error'])>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ \App\Models\Accounting\TransactionAuditLog::LABELS[$log->action] ?? $log->action }}</td>
                <td>{{ $log->user->name ?? '-' }}</td>
                <td>{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-muted text-center">履歴はありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
