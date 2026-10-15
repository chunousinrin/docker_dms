<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    {{-- AdminLTEのスタイルをそのまま適用してデザインを統一 --}}
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
        </h5>
    </div>

    <table class="table table-bordered table-sm">
        <tr>
            <th class="table-th">取引年月日</th>
            <td>{{ $transaction->transaction_date }}</td>
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
            <th class="table-th">備考</th>
            <td>{{ $transaction->note ?? '-' }}</td>
        </tr>
    </table>

    <h5 class="mt-4 mb-2 h6 font-weight-bold" style="color: var(--main-color);">
        <i class="fas fa-file-pdf mr-1"></i> 証跡書類
    </h5>
    <div class="list-group">
        @forelse($transaction->files as $file)
        <a href="/transaction-files/{{ $file->id }}/view" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
            <div>
                <span class="badge badge-info mr-2">{{ $file->file_type }}</span>
                {{ $file->original_filename }}
            </div>
            <i class="fas fa-external-link-alt text-primary" style="font-size: 0.8rem;"></i>
        </a>
        @empty
        <p class="text-muted small pl-1">添付ファイルはありません</p>
        @endforelse
    </div>

</body>

</html>