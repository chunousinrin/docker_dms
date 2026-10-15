@extends('layouts.adminlte')

@section('title', '組合員詳細')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pl-3">
    <div class="row">
        <h1>{{ $member->member_number }}：{{ $member->name1 }} {{ $member->name2 }} 様</h1>
        @can('update', $member)
        <a href="{{ route('members.edit', $member) }}" class="btn btn-light shadow-sm btn-sm ml-2 pt-1" title="基本情報の編集">
            <i class="fas fa-edit"></i>
        </a>
        @endcan
    </div>
    <a href="{{ route('members.index') }}" class="btn btn-main btn-sm px-4">
        <i class="fas fa-list mr-1"></i>組合員一覧
    </a>
</div>
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- 基本情報サマリ --}}
<div class="card">
    <div class="card-body row">
        <div class="col-md-3"><strong>フリガナ</strong><br>{{ $member->furigana }}</div>
        <div class="col-md-3"><strong>所属地区／字</strong><br>{{ optional($member->district)->name }} / {{ optional($member->section)->name }}</div>
        <div class="col-md-3"><strong>組合員区分</strong><br>{{ $member->member_type }}</div>
        <div class="col-md-3"><strong>在籍状況</strong><br>
            @if ($member->is_active) <span class="badge badge-success">在籍中</span>
            @else <span class="badge badge-secondary">脱退済（{{ optional($member->withdrawn_date)->format('Y-m-d') }}）</span>
            @endif
        </div>
        <div class="col-md-3 mt-2"><strong>現在の出資</strong><br>{{ $member->current_capital['units'] }} 口 / {{ number_format($member->current_capital['amount']) }} 円</div>
        <div class="col-md-3 mt-2"><strong>現在の所有山林面積</strong><br>{{ $member->current_forest_area ?? 0 }} ha</div>
        <div class="col-md-3 mt-2"><strong>預り書発行回数</strong><br>{{ $member->receipt_issue_count }} 回</div>
        <div class="col-md-3 mt-2"><strong>総代／広報送付</strong><br>
            @if ($member->is_soudai) <span class="badge badge-info">総代</span> @endif
            @if ($member->newsletter_delivery) <span class="badge badge-primary">広報送付対象</span> @else <span class="badge badge-secondary">送付対象外</span> @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#tab-capital" data-toggle="tab">出資増減</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-forest" data-toggle="tab">所有山林面積増減</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-receipt" data-toggle="tab">出資預り書発行</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-newsletter" data-toggle="tab">広報誌発送履歴</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">

            {{-- 出資増減 --}}
            <div class="tab-pane active" id="tab-capital">
                @can('update', $member)
                <form method="POST" action="{{ route('members.capital-transactions.store', $member) }}" class="form-row mb-3">
                    @csrf
                    <div class="col-md-2"><input type="text" name="transaction_date" class="form-control datepicker" required placeholder="異動発生日"></div>
                    <div class="col-md-2">
                        <select name="transaction_type" class="form-control" required>
                            <option value="increase">増口</option>
                            <option value="decrease">減口</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" name="units" class="form-control" placeholder="口数" required></div>
                    <div class="col-md-2"><input type="number" name="amount" class="form-control" placeholder="金額" required></div>
                    <div class="col-md-2"><input type="text" name="reason" class="form-control" placeholder="事由"></div>
                    <div class="col-md-2"><button class="btn btn-primary btn-block">登録</button></div>
                </form>
                @endcan
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>種別</th>
                            <th>口数</th>
                            <th>金額</th>
                            <th>残高(口)</th>
                            <th>残高(円)</th>
                            <th>事由</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->capitalTransactions as $t)
                        <tr>
                            <td>{{ $t->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $t->transaction_type === 'increase' ? '増口' : '減口' }}</td>
                            <td>{{ $t->units }}</td>
                            <td>{{ number_format($t->amount) }}</td>
                            <td>{{ $t->balance_units_after }}</td>
                            <td>{{ number_format($t->balance_amount_after) }}</td>
                            <td>{{ $t->reason }}</td>
                            <td>
                                @can('update', $member)
                                <form method="POST" action="{{ route('members.capital-transactions.destroy', [$member, $t->id]) }}" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">登録がありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 所有山林面積増減 --}}
            <div class="tab-pane" id="tab-forest">
                @can('update', $member)
                <form method="POST" action="{{ route('members.forest-area-transactions.store', $member) }}" class="form-row mb-3">
                    @csrf
                    <div class="col-md-2"><input type="text" name="transaction_date" class="form-control datepicker" required placeholder="異動発生日"></div>
                    <div class="col-md-2">
                        <select name="transaction_type" class="form-control" required>
                            <option value="increase">増加</option>
                            <option value="decrease">減少</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" step="0.0001" name="area_ha" class="form-control" placeholder="面積(ha)" required></div>
                    <div class="col-md-2"><input type="text" name="location" class="form-control" placeholder="所在地"></div>
                    <div class="col-md-2"><input type="text" name="reason" class="form-control" placeholder="事由"></div>
                    <div class="col-md-2"><button class="btn btn-primary btn-block">登録</button></div>
                </form>
                @endcan
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>種別</th>
                            <th>面積(ha)</th>
                            <th>残高(ha)</th>
                            <th>所在地</th>
                            <th>事由</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->forestAreaTransactions as $t)
                        <tr>
                            <td>{{ $t->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $t->transaction_type === 'increase' ? '増加' : '減少' }}</td>
                            <td>{{ $t->area_ha }}</td>
                            <td>{{ $t->balance_area_ha_after }}</td>
                            <td>{{ $t->location }}</td>
                            <td>{{ $t->reason }}</td>
                            <td>
                                @can('update', $member)
                                <form method="POST" action="{{ route('members.forest-area-transactions.destroy', [$member, $t->id]) }}" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">登録がありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 出資預り書発行 --}}
            <div class="tab-pane" id="tab-receipt">
                @can('update', $member)
                <form method="POST" action="{{ route('members.deposit-receipts.store', $member) }}" class="form-row mb-3">
                    @csrf
                    <div class="col-md-2"><input type="text" name="issue_date" class="form-control datepicker" required placeholder="発行日"></div>
                    <div class="col-md-3"><input type="text" name="reason" class="form-control" placeholder="発行事由（新規/再発行 等）"></div>
                    <div class="col-md-3"><input type="text" name="issued_by" class="form-control" placeholder="発行担当者"></div>
                    <div class="col-md-4"><button class="btn btn-primary">発行を記録（次回：{{ $member->receipt_issue_count + 1 }}回目）</button></div>
                </form>
                @endcan
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>発行回数</th>
                            <th>発行日</th>
                            <th>発行時出資金額</th>
                            <th>事由</th>
                            <th>担当者</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->depositReceipts as $r)
                        <tr>
                            <td>第{{ $r->issue_number }}回</td>
                            <td>{{ $r->issue_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($r->capital_amount_at_issue) }} 円</td>
                            <td>{{ $r->reason }}</td>
                            <td>{{ $r->issued_by }}</td>
                            <td>
                                @can('update', $member)
                                <form method="POST" action="{{ route('members.deposit-receipts.destroy', [$member, $r->id]) }}" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">発行履歴がありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 広報誌発送履歴 --}}
            <div class="tab-pane" id="tab-newsletter">
                @can('update', $member)
                <p class="text-muted">
                    号の登録・在籍組合員への一括発送は
                    <a href="{{ route('newsletter-issues.index') }}">広報誌 号管理</a> から行えます。
                    ここでは個別の追加・修正のみ行います。
                </p>
                <form method="POST" action="{{ route('members.newsletter-shipments.store', $member) }}" class="form-row mb-3">
                    @csrf
                    <div class="col-md-3">
                        <select name="newsletter_issue_id" class="form-control">
                            <option value="">号を選択（任意）</option>
                            @foreach ($newsletterIssues as $issue)
                            <option value="{{ $issue->id }}">{{ $issue->issue_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" name="sent_date" class="form-control datepicker" required placeholder="発送日 / 変更日"></div>
                    <div class="col-md-2">
                        <select name="method" class="form-control" required>
                            <option value="mail">郵送</option>
                            <option value="hand">手渡し</option>
                            <option value="other">その他</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control" required>
                            <option value="sent">送付済</option>
                            <option value="returned">宛先不明で返戻</option>
                            <option value="skipped">送付対象外</option>
                        </select>
                    </div>
                    <div class="col-md-2"><button class="btn btn-primary btn-block">登録</button></div>
                </form>
                @endcan
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>発送日</th>
                            <th>号数</th>
                            <th>方法</th>
                            <th>状況</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->newsletterShipments as $s)
                        <tr>
                            <td>{{ $s->sent_date->format('Y-m-d') }}</td>
                            <td>{{ optional($s->issue)->issue_name }}</td>
                            <td>{{ ['mail' => '郵送', 'hand' => '手渡し', 'other' => 'その他'][$s->method] }}</td>
                            <td>{{ ['sent' => '送付済', 'returned' => '返戻', 'skipped' => '対象外'][$s->status] }}</td>
                            <td>
                                @can('update', $member)
                                <form method="POST" action="{{ route('members.newsletter-shipments.destroy', [$member, $s->id]) }}" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">発送履歴がありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection