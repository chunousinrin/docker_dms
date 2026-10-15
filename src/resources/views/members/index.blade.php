@extends('layouts.adminlte')

@section('title', '組合員一覧')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    @can('create', \App\Models\Master\Member::class)
    <a href="{{ route('members.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規登録
    </a>
    @endcan
</div>
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card search-card mb-4">
    <div class="card-body p-4">
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="keyword" value="{{ request('keyword') }}"
                class="form-control mr-2" placeholder="組合員番号・氏名・フリガナ">

            <select name="district_id" class="form-control mr-2">
                <option value="">所属地区（すべて）</option>
                @foreach ($districts as $d)
                <option value="{{ $d->id }}" @selected((string) request('district_id')===(string) $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>

            <select name="status" class="form-control mr-2">
                <option value="">在籍状況（すべて）</option>
                <option value="active" @selected(request('status')==='active' )>在籍中</option>
                <option value="withdrawn" @selected(request('status')==='withdrawn' )>脱退済</option>
            </select>

            <button class="btn btn-primary mr-2">検索</button>
        </form>

    </div>
</div>

<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i> 組合員一覧 ({{ number_format($members->total()) }} 件)
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>組合員番号</th>
                    <th>氏名</th>
                    <th>フリガナ</th>
                    <th>所属地区</th>
                    <th>組合員区分</th>
                    <th>加入年月日</th>
                    <th>在籍状況</th>
                    <th>総代</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                <tr>
                    <td>{{ $member->member_number }}</td>
                    <td>{{ $member->name1 }} {{ $member->name2 }}</td>
                    <td>{{ $member->furigana }}</td>
                    <td>{{ optional($member->district)->name }}</td>
                    <td>{{ $member->member_type }}</td>
                    <td>{{ optional($member->joined_date)->format('Y-m-d') }}</td>
                    <td>
                        @if ($member->is_active)
                        <span class="badge badge-success">在籍中</span>
                        @else
                        <span class="badge badge-secondary">脱退済</span>
                        @endif
                    </td>
                    <td>@if ($member->is_soudai) <span class="badge badge-info">総代</span> @endif</td>
                    <td style="width:0;" class="text-right">
                        <a href="{{ route('members.show', $member) }}" class="btn btn-sm shadow-sm btn-light" title="詳細">
                            <i class="fas fa-info"></i>
                        </a>
                        @can('update', $member)
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-sm shadow-sm btn-light" title="編集">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">該当する組合員がいません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $members->links() }}
    </div>
</div>
@endsection