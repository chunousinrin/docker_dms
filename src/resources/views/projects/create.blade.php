@extends('layouts.adminlte')

@section('title', '事業登録')

@push('js')
@vite('resources/js/partials/projects.js')
@endpush

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>事業登録</h1>
    <a href="{{ route('projects.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fa fa-list mr-1"></i> 案件一覧
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('projects.store') }}"
    method="POST"
    id="project-form"
    data-route-worker-groups-active="{{ route('workerGroups.active') }}"
    data-route-order-no-candidates="{{ route('projects.orderNoCandidates') }}"
    data-route-pdf-preview="{{ route('projects.pdfPreview') }}">
    @csrf
    <div class="card card-tabs shadow-sm border-0" style="border-radius: 8px; overflow: hidden;">
        <!-- 上部タブナビゲーション -->
        <div class="card-header p-0 border-bottom-0 bg-white">
            <ul class="nav nav-tabs pl-4 pt-2" id="editTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold py-3 px-4" id="basic-tab" data-toggle="tab" href="#basic" role="tab" style="border-left-color: #dee2e6;">
                        <i class=" fas fa-file-alt mr-2 text-primary"></i>1. 指示書共通
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3 px-4" id="work-tab" data-toggle="tab" href="#work" role="tab">
                        <i class="fas fa-sliders-h mr-2 text-success"></i>2. 種類別項目
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3 px-4" id="other-tab" data-toggle="tab" href="#other" role="tab">
                        <i class="fas fa-info-circle mr-2 text-info"></i>3. 事業仔細
                    </a>
                </li>
            </ul>
        </div>
        @include('projects.form')
        <!-- フッター：固定アクション -->
        <div class="card-footer d-flex justify-content-between border-top">
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-light px-4">
                <i class="fa fa-arrow-left mr-1"></i> キャンセル
            </a>
            <div>
                <button type="button" id="preview-btn" class="btn btn-sm btn-secondary px-5 shadow-sm">
                    <i class="far fa-file-pdf mr-1"></i> PDFプレビュー
                </button>


                <button type="submit" class="btn btn-sm btn-primary px-5 shadow-sm">
                    <i class="fas fa-save mr-1"></i> この内容で事業登録する
                </button>
            </div>
        </div>
    </div>

</form>

@endsection