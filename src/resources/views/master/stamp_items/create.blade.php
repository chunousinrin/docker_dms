@extends('layouts.adminlte')

@section('title', '品目追加')

@section('content_header')
<h1>品目追加</h1>
@stop

@section('content')
@include('stamps.partials.flash')
<div class="row">
    <div class="col-lg-6">
        <div class="card card-primary">
            <form method="POST" action="{{ route('master.stamp_items.store') }}">
                @csrf
                @include('master.stamp_items._form')
                <div class="card-footer">
                    <button class="btn btn-primary">登録</button>
                    <a href="{{ route('master.stamp_items.index') }}" class="btn btn-default">戻る</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop