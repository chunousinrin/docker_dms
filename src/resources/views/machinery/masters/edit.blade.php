@extends('layouts.adminlte')

@section('title', '重機編集')

@section('content_header')
<div class="d-flex justify-content-between align-items-center pr-2">
    <h1 class="ml-2 mb-0">{{ $machinery->machine_name }}</h1>

    <div class="">
        <a href="{{ route('machinery-masters.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 重機一覧
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card search-card mb-4">
        <div class="card-body">

            <form action="{{ route('machinery-masters.update', $machinery) }}"
                method="POST">

                @csrf
                @method('PUT')

                @include('machinery.masters.form', [
                'machinery' => $machinery
                ])

            </form>
        </div>
    </div>
</div>

@endsection