@extends('layouts.adminlte')

@section('title', '案件詳細')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">案件詳細: {{ $project->project_name }}</h1>
    <div>
        <a href="{{ route('projects.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fas fa-list mr-1"></i> 案件一覧
        </a>
        <a href="{{ route('projects.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
            <i class="fa-solid fa-plus"></i> 新規作成
        </a>
    </div>
</div>
<style>
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-color: var(--dms-green) !important;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="nav nav-pills nav-fill" id="projectTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="order-tab" data-toggle="tab" href="#order" role="tab">作業指示書</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="projects-tab" data-toggle="tab" href="#projects" role="tab">事業内容</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="costs-tab" data-toggle="tab" href="#costs" role="tab">費用</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab">発行済み請求書</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content mt-3" id="projectTabContent">

            @include('projects.show_tab.instructions')
            @include('projects.show_tab.projects')
            @include('projects.show_tab.costs')
            @include('projects.show_tab.invoices')

        </div>
    </div>
</div>
@endsection