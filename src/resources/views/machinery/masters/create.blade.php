@extends('layouts.adminlte')

@section('title', '重機登録')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">重機登録</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 重機一覧
        </a>
    </div>
</div>
@stop

@section('content')

<div class=" card search-card mb-0">
    <div class="card-body">

        <form action="{{ route('machinery-masters.store') }}"
            method="POST">

            @csrf

            @include('machinery.masters.form')

        </form>

    </div>
</div>

@endsection