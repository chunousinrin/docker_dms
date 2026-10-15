@if (session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="icon fas fa-check"></i> {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h6><i class="icon fas fa-ban"></i> 入力内容を確認してください</h6>
    <ul class="mb-0">
        @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('warning'))
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="icon fas fa-exclamation-triangle"></i> {{ session('warning') }}
</div>
@endif