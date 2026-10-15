{{-- 請求書一覧 --}}
<div class="tab-pane fade" id="invoices" role="tabpanel">
    <table class="table-custom">
        <thead>
            <tr>
                <th scope="col">請求番号</th>
                <th scope="col">請求日</th>
                <th scope="col">顧客名</th>
                <th scope="col">合計金額</th>
                <th scope="col">入金状況</th>
            </tr>
        </thead>
        <tbody>
            @forelse($project->invoices as $invoice)
            <tr>
                <td>
                    <a href="{{ route('invoices.edit', $invoice) }}">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td>{{ $invoice->billing_date ? $invoice->billing_date->format('Y/m/d') : '-' }}</td>
                <td>{{ $invoice->customer_name }}</td>
                <td>{{ number_format($invoice->subtotal_amount) }}円</td>
                <td>{{ $invoice->payment_status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">請求データはありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>