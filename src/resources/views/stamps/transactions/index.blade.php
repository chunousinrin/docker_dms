@extends('layouts.adminlte')

@section('title', '入出庫履歴')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>入出庫履歴</h1>
    <a href="{{ route('stamps.transactions.create') }}" class="btn btn-sm btn-main shadow-sm px-3">
        <i class="fas fa-plus"></i> 入出庫登録
    </a>
</div>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="card search-card">
    <div class="card-body">
        <form method="GET" action="{{ route('stamps.transactions.index') }}">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="field-label">品目</label>
                    <select name="stamp_item_id" class="form-control form-control-sm">
                        <option value="">品目：すべて</option>
                        @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(request('stamp_item_id')==$item->id)>
                            {{ $item->category_label }} - {{ $item->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="field-label">種別</label>
                    <select name="type" class="form-control form-control-sm">
                        <option value="">種別：すべて</option>
                        @foreach (\App\Models\StampTransaction::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(request('type')===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label class="field-label">記録者</label>
                    <select name="recorder_id" class="form-control form-control-sm">
                        <option value="">記録者：すべて</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id')==$user->id)>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="field-label">発行日（期間）</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="from" value="{{ request('from') }}" class="form-control datepicker text-center" placeholder="開始日">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0 border-right-0">～</span>
                        </div>
                        <input type="text" name="to" value="{{ request('to') }}" class="form-control datepicker text-center" placeholder="終了日">
                    </div>
                </div>
            </div>

            <div class="action-bar d-flex justify-content-between align-items-center">
                <a href="{{ route('stamps.transactions.index') }}" class="btn-reset btn btn-sm shadow-sm">
                    <i class="fas fa-undo-alt mr-1"></i> 条件をリセット
                </a>
                <button class="btn btn-sm btn-primary px-4 shadow-sm shadow-sm">
                    <i class="fas fa-search mr-2"></i> この条件で絞り込む
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card search-card mb-0">
    <div class="card-header">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>履歴一覧({{ number_format($transactions->total()) }} 件)
        </h5>
    </div>
    <div class="card-body table-responsive-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>品目</th>
                    <th>種別</th>
                    <th class="text-right">数量</th>
                    <th>購入先 / 使用先</th>
                    <th>メモ</th>
                    <th>記録者</th>
                    <th>登録日時</th>
                    <th>確認</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                <tr>
                    <td>{{ $t->transacted_on->format('Y-m-d') }}</td>
                    <td>{{ $t->item->category_label }} - {{ $t->item->name }}</td>
                    <td>
                        @php
                        $badge = ['in' => 'success', 'out' => 'danger', 'adjust' => 'warning'][$t->type] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $badge }}">{{ $t->type_label }}</span>
                    </td>
                    <td class="text-right {{ $t->quantity < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $t->quantity > 0 ? '+' : '' }}{{ number_format($t->quantity) }}
                    </td>
                    <td>{{ $t->counterparty }}</td>
                    <td>
                        {{ $t->memo }}
                        @if ($t->audit_id && auth()->user()->can('admin'))
                        <a href="{{ route('stamps.audits.show', $t->audit_id) }}">（棚卸 #{{ $t->audit_id }}）</a>
                        @endif
                    </td>
                    <td>
                        {{ $t->recorder_name }}
                        @if ($t->ip_address && auth()->user()->can('admin'))
                        <br><small class="text-muted">{{ $t->ip_address }}</small>
                        @endif
                    </td>
                    <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if (! $t->isVerifiable())
                        <span class="text-muted">-</span>
                        @elseif ($t->verification)
                        @if ($t->verification->status === 'confirmed')
                        <span class="badge badge-success">確認済</span>
                        @else
                        <span class="badge badge-danger">差異あり</span>
                        <br><small class="text-danger">
                            記録 {{ number_format($t->verification->expected_qty) }} /
                            実物 {{ number_format($t->verification->actual_qty) }}
                        </small>
                        @endif
                        <br><small class="text-muted">
                            {{ $t->verification->verifier->name ?? '-' }}
                            {{ $t->verification->verified_at->format('m/d H:i') }}
                        </small>
                        @if ($t->verification->memo)
                        <br><small>{{ $t->verification->memo }}</small>
                        @endif
                        @else
                        <span class="badge badge-warning">未確認</span>
                        @can('verify-transactions')
                        @if ($t->canBeVerifiedBy(auth()->user()))
                        <a href="{{ route('stamps.transactions.verify.create', $t) }}"
                            class="btn btn-xs btn-primary ml-1">確認する</a>
                        @endif
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">履歴がありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
</div>
@stop