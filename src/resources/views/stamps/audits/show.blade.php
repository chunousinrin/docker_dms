@extends('layouts.adminlte')

@section('title', '棚卸詳細')

@section('content_header')
<h1>棚卸詳細 #{{ $audit->id }}</h1>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="card card-outline card-primary">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-2">実施日</dt>
            <dd class="col-sm-10">{{ $audit->audited_on->format('Y-m-d') }}</dd>
            <dt class="col-sm-2">実施者</dt>
            <dd class="col-sm-10">{{ $audit->auditor->name ?? '-' }}</dd>
            <dt class="col-sm-2">メモ</dt>
            <dd class="col-sm-10">{{ $audit->memo ?: '-' }}</dd>
            <dt class="col-sm-2">登録日時</dt>
            <dd class="col-sm-10">{{ $audit->created_at->format('Y-m-d H:i:s') }}</dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table text-nowrap">
            <thead>
                <tr>
                    <th>品目</th>
                    <th class="text-right">額面</th>
                    <th class="text-right">帳簿数</th>
                    <th class="text-right">実数</th>
                    <th class="text-right">差異</th>
                    <th class="text-right">差異金額</th>
                    <th>理由</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($audit->lines as $line)
                <tr class="{{ $line->diff !== 0 ? 'table-warning' : '' }}">
                    <td>{{ $line->item->name }}</td>
                    <td class="text-right">¥{{ number_format($line->face_value) }}</td>
                    <td class="text-right">{{ number_format($line->book_qty) }}</td>
                    <td class="text-right">{{ number_format($line->counted_qty) }}</td>
                    <td class="text-right {{ $line->diff !== 0 ? 'text-danger font-weight-bold' : '' }}">
                        {{ $line->diff > 0 ? '+' : '' }}{{ number_format($line->diff) }}
                    </td>
                    <td class="text-right">
                        {{ $line->diff_amount < 0 ? '-' : '' }}¥{{ number_format(abs($line->diff_amount)) }}
                    </td>
                    <td>{{ $line->note }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">差異金額合計</td>
                    <td class="text-right">{{ $totalDiffAmount < 0 ? '-' : '' }}¥{{ number_format(abs($totalDiffAmount)) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('stamps.audits.index') }}" class="btn btn-default">一覧へ戻る</a>
    </div>
</div>
@stop