@extends('layouts.adminlte')

@section('title', '棚卸監査')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1>棚卸監査</h1>
    <div>
        <a href="{{ route('stamps.audits.create') }}" class="btn btn-sm btn-main shadow-sm px-3">
            <i class="fas fa-clipboard-check"></i> 棚卸を実施
        </a>
    </div>
</div>
@stop

@section('content')
@include('stamps.partials.flash')

<div class="card search-card">
    <div class="card-body table-responsive-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>#</th>
                    <th>実施日</th>
                    <th>実施者</th>
                    <th>差異</th>
                    <th>メモ</th>
                    <th>登録日時</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($audits as $audit)
                <tr>
                    <td>{{ $audit->id }}</td>
                    <td>{{ $audit->audited_on->format('Y-m-d') }}</td>
                    <td>{{ $audit->auditor->name ?? '-' }}</td>
                    <td>
                        @if ($audit->diff_lines_count > 0)
                        <span class="badge badge-warning">{{ $audit->diff_lines_count }} 品目に差異</span>
                        @else
                        <span class="badge badge-success">差異なし</span>
                        @endif
                    </td>
                    <td>{{ $audit->memo }}</td>
                    <td>{{ $audit->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-right">
                        <a href="{{ route('stamps.audits.show', $audit) }}" class="btn btn-xs btn-info">詳細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">棚卸の記録がありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $audits->links() }}</div>
</div>
@stop