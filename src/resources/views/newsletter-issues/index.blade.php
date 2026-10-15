@extends('layouts.adminlte')

@section('title', '広報誌 号管理')

@section('content_header')
<h1>広報誌 号管理</h1>
@endsection

@section('content')
@if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="card">
    <div class="card-header"><strong>新しい号を登録</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('newsletter-issues.store') }}" class="form-row">
            @csrf
            <div class="col-md-4"><input type="text" name="issue_name" class="form-control" placeholder="号名（例：広報 令和8年8月号）" required></div>
            <div class="col-md-3"><input type="date" name="published_date" class="form-control"></div>
            <div class="col-md-3"><input type="text" name="remarks" class="form-control" placeholder="備考"></div>
            <div class="col-md-2"><button class="btn btn-primary btn-block">登録</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-sm align-middle">
            <thead>
                <tr>
                    <th>発行年月日</th>
                    <th>号名</th>
                    <th>発送済件数</th>
                    <th style="width:420px">在籍・広報送付対象へ一括発送記録</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($issues as $issue)
                <tr>
                    <td style="width:130px">{{ optional($issue->published_date)->format('Y-m-d') }}</td>
                    <td>
                        {{ $issue->issue_name }}
                        @if ($issue->remarks)
                        <br><small class="text-muted">{{ $issue->remarks }}</small>
                        @endif
                    </td>
                    <td>{{ $issue->shipments_count }} 件</td>
                    <td>
                        <form method="POST" action="{{ route('newsletter-issues.ship-to-all', $issue) }}"
                            class="form-row m-0"
                            onsubmit="return confirm('未発送の在籍・広報送付対象組合員に、この号の発送履歴を一括登録します。よろしいですか？')">
                            @csrf
                            <div class="col-5"><input type="date" name="sent_date" class="form-control form-control-sm" required></div>
                            <div class="col-4">
                                <select name="method" class="form-control form-control-sm">
                                    <option value="mail">郵送</option>
                                    <option value="hand">手渡し</option>
                                    <option value="other">その他</option>
                                </select>
                            </div>
                            <div class="col-3"><button class="btn btn-sm btn-primary btn-block">一括発送</button></div>
                        </form>
                    </td>
                    <td style="width:70px">
                        <form method="POST" action="{{ route('newsletter-issues.destroy', $issue) }}" onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">まだ号が登録されていません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $issues->links() }}
    </div>
</div>

<p class="text-muted">
    「一括発送」は、在籍中かつ「広報送付」フラグが立っている組合員のうち、
    この号でまだ発送履歴が無い組合員だけに、送付済としてまとめて記録します。
    宛先不明の返戻や個別対応が発生した場合は、各組合員の詳細画面（発送履歴タブ）から
    該当行を編集・削除してください。
</p>
@endsection