@extends('layouts.adminlte')

@section('title', '職員一覧')

@section('content_header')
@endsection

@section('content')

<div class="row g-3 mt-5">
    @forelse($users as $user)

    <a href="{{ route('time_records.show', $user->id) }}" class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h3 class="text-center">{{ $user->name }}</h3>
            </div>
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="small-box-footer">
                <i class="fas fa-arrow-circle-right"></i>
            </div>
        </div>
    </a>

    @empty
    <div class="col-12">
        <div class="alert alert-info text-center" role="alert">
            対象の職員がいません
        </div>
    </div>
    @endforelse

</div>
@endsection

@section('css')
<style>
    /* サイドバーを非表示にし、メインコンテンツの余白をなくす場合 */
    body:not(.layout-fixed) .main-sidebar,
    body:not(.layout-fixed) .main-header {
        Display: none !important;
    }

    body:not(.layout-fixed) .content-wrapper,
    body:not(.layout-fixed) .main-footer {
        Margin-left: 0 !important;
    }
</style>
@endsection