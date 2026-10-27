@extends('layouts.adminlte')

@section('title', '発注書作成')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>発注書作成</h1>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 発注書一覧
    </a>
</div>
@stop

@section('content')
<form method="POST"
    action="{{ route('purchase-orders.store') }}"
    autocomplete="off">

    @csrf

    @include('purchase_orders.form')
</form>
@endsection