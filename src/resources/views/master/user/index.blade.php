@extends('layouts.adminlte')

@section('title', 'ユーザー管理')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('users.create') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-plus mr-1"></i> 新規登録
    </a>
</div>
@if(session('generated_password'))
<div class="alert alert-warning">
    <strong>初期パスワード：</strong>
    {{ session('generated_password') }}
</div>
@endif
@endsection

@section('content')
<div class="card search-card mb-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h5 class="m-0 h6 font-weight-bold text-success">
            <i class="fas fa-list-ul mr-1"></i>ユーザー一覧 ({{ number_format($users->total()) }} 件)
        </h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive-custom">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th class="text-center px-1">ID</th>
                        <th>氏名</th>
                        <th>部署</th>
                        <th>課</th>
                        <th>役職</th>
                        <th>権限</th>
                        <th>状態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center px-1">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->section }}</td>
                        <td>{{ $user->position }}</td>
                        <td>{{ $user->auth_type_label }}</td>
                        <td>
                            <span class="badge {{ $user->used == 1 ? 'badge-success' : 'badge-danger' }} py-1 px-2" style="font-size: 0.7rem;">
                                {{ $user->used == 1 ? '有効' : '無効' }}
                            </span>
                        </td>
                        <td style="width:0;white-space:nowrap;">
                            <a href="{{ route('users.edit', $user) }}"
                                data-toggle="modal"
                                data-target="#userModal"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-department="{{ $user->department }}"
                                data-section="{{ $user->section }}"
                                data-position="{{ $user->position }}"
                                data-manager_id="{{ $user->manager_id }}"
                                data-auth_type="{{ $user->auth_type }}"
                                data-used="{{ $user->used }}"
                                data-update-url="{{ route('users.update', $user->id) }}"
                                class="btn btn-sm shadow-sm btn-light" style="padding-left: 0.5em;padding-right:0.2em;">
                                <i class="fas fa-edit" style="color:#343a40"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ページャ --}}
    <div class="card-footer clearfix">
        {{ $users->links() }}
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- ▼ id="userForm" を追加 --}}
            <form method="POST" action="" id="userForm">

                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">ユーザー編集</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">氏名</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">メール</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">部署</label>
                        <input type="text" name="department" id="department" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">課</label>
                        <input type="text" name="section" id="section" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">役職</label>
                        <input type="text" name="position" id="position" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">上司</label>
                        {{-- ▼ id="manager_id" を追加 --}}
                        <select name="manager_id" id="manager_id" class="form-control">
                            <option value="">なし</option>
                            @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">
                                {{ $manager->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">権限</label>
                        <select name="auth_type" id="auth_type" class="form-control">
                            <option value="1"> 一般 </option>
                            <option value="0"> 管理者 </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1 small">アカウントの状態</label>
                        <select name="used" id="used" class="form-control">
                            <option value="1"> 有効 </option>
                            <option value="0"> 無効 </option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary btn-block">
                        <i class="fas fa-save mr-2"> </i>情報を更新する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $('#userModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        modal.find('#modalTitle').text('ユーザー情報の編集');
        // ここが正しく動くようになります
        modal.find('#userForm').attr('action', button.data('update-url'));

        modal.find('#name').val(button.data('name'));
        modal.find('#email').val(button.data('email'));
        modal.find('#department').val(button.data('department'));
        modal.find('#section').val(button.data('section'));
        modal.find('#position').val(button.data('position'));
        modal.find('#manager_id').val(button.data('manager_id'));
        modal.find('#auth_type').val(button.data('auth_type'));
        modal.find('#used').val(button.data('used'));
    });
</script>
@endsection