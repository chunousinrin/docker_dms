@extends('layouts.adminlte')

@section('title', '業務日報編集')

@section('content_header')
<h1>業務日報編集</h1>
@endsection

@section('content')
@if (session('info'))
<div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('daily-reports.update', $dailyReport) }}" method="POST">
            @csrf
            @method('PUT')
            @include('daily_reports._form')

            <button type="submit" class="btn btn-sm btn-primary px-5"><i class="fas fa-save mr-1"></i>更新</button>
            <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-secondary px-5"><i class="fas fa-arrow-left mr-1"></i>戻る</a>
        </form>
    </div>
</div>
@endsection