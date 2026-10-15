@extends('layouts.adminlte')

@section('title', '履歴の確認')

@section('content_header')
<h1>入出庫履歴の確認</h1>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="row">
    <div class="col-lg-7">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">記録内容</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">日付</dt>
                    <dd class="col-sm-8">{{ $transaction->transacted_on->format('Y-m-d') }}</dd>
                    <dt class="col-sm-4">品目</dt>
                    <dd class="col-sm-8">{{ $transaction->item->category_label }} - {{ $transaction->item->name }}</dd>
                    <dt class="col-sm-4">種別</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-{{ $transaction->type === 'in' ? 'success' : 'danger' }}">
                            {{ $transaction->type_label }}
                        </span>
                    </dd>
                    <dt class="col-sm-4">記録上の数量</dt>
                    <dd class="col-sm-8"><strong class="h5">{{ number_format(abs($transaction->quantity)) }}</strong></dd>
                    <dt class="col-sm-4">購入先 / 使用先</dt>
                    <dd class="col-sm-8">{{ $transaction->counterparty ?: '-' }}</dd>
                    <dt class="col-sm-4">メモ</dt>
                    <dd class="col-sm-8">{{ $transaction->memo ?: '-' }}</dd>
                    <dt class="col-sm-4">記録者</dt>
                    <dd class="col-sm-8">{{ $transaction->recorder_name }}</dd>
                    <dt class="col-sm-4">登録日時</dt>
                    <dd class="col-sm-8">{{ $transaction->created_at->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card card-primary">
            <form method="POST" action="{{ route('stamps.transactions.verify.store', $transaction) }}"
                onsubmit="return confirm('この内容で確認を記録します。確認記録は後から修正できません。よろしいですか？');">
                @csrf
                <div class="card-header">
                    <h3 class="card-title">実物の確認</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="actual_qty">
                            {{ $transaction->type === 'out' ? '実際に取り出された枚数' : '実際に入庫された枚数' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" name="actual_qty" id="actual_qty" required autofocus
                            value="{{ old('actual_qty') }}"
                            class="form-control form-control-lg @error('actual_qty') is-invalid @enderror"
                            style="max-width:200px">
                        @error('actual_qty') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        <small class="text-muted">
                            実物（または納品物・使用済みの控え）を確認して、枚数を入力してください。
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="memo">メモ（記録と一致しない場合は必須）</label>
                        <input type="text" name="memo" id="memo" maxlength="255" value="{{ old('memo') }}"
                            class="form-control @error('memo') is-invalid @enderror">
                        @error('memo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">確認を記録</button>
                    <a href="{{ route('stamps.transactions.index', ['verification' => 'unverified']) }}"
                        class="btn btn-default">戻る</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop