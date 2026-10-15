@extends('layouts.adminlte')

@section('title', '会社情報')

@section('content')

<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-1">
        <i class="fas fa-check-circle mr-1"></i>
        {{ session('success') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    <form action="{{ route('company.update') }}" method="POST">
        @csrf
        @method('PUT')

        @foreach($branches as $branch)
        <div class="card search-card mb-4 mt-1">

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center px-2">
                    <h3 class="card-title"> <i class="fas fa-building mr-1"></i> {{ $branch->name }} </h3>
                    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                        <i class="fas fa-save mr-1"></i>
                        保存
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">事業所名</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][name]' value='{{ old("branches.{$branch->id}.name", $branch->name) }}'>
                        </td>
                        <td rowspan="6" class="form-group text-center">
                            <label class="font-weight-bold text-dark mb-1 small">登録印影</label>

                            <div class="p-2 bg-white">
                                @if($branch->seal_path)
                                <img src="data:image/png;base64,{{ base64_encode($branch->seal_path) }}" style="right: 2mm;width: 19mm;opacity: 0.65;" class="shadow">
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">代表役職・氏名</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][representative]' value='{{ old("branches.$branch->id.representative", $branch->representative) }}'>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">郵便番号</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][postal_code]' value='{{ old("branches.$branch->id.postal_code", $branch->postal_code) }}'>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">住所</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][address]' value='{{ old("branches.$branch->id.address", $branch->address) }}'>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">電話番号</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][phone]' value='{{ old("branches.$branch->id.phone", $branch->phone) }}'>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-group">
                            <label class="font-weight-bold text-dark mb-1 small">FAX</label>
                            <input type="text" class="form-control" name='branches[{{ $branch->id }}][fax]' value='{{ old("branches.$branch->id.fax", $branch->fax) }}'>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
        @endforeach
    </form>
</div>

@endsection