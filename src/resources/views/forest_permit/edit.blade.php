@extends('layouts.adminlte')

@section('title', '許認可登録')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">登録済み許認可編集</h1>

    <div class="">
        <a href="{{ route('forest-permit.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 許認可一覧
        </a>
    </div>
</div>
@stop


@section('content')

<div class="container-fluid">

    <form method="POST" action="{{ route('forest-permit.update', $application) }}">
        @csrf
        @method('PUT')

        @include('forest_permit._form')

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> 登録
        </button>

        <a href="{{ route('forest-permit.index') }}" class="btn btn-secondary">
            戻る
        </a>

    </form>
</div>

@endsection