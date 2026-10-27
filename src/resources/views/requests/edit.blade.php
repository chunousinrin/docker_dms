@extends('layouts.adminlte')

@section('title', '稟議書編集')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>稟議書編集</h1>
    <a href="{{ route('requests.index', ['mode' => 'all']) }}" class="btn btn-sm btn-main shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 稟議書一覧
    </a>
</div>
@endsection

@section('content')
<form id="mainForm" method="POST" action="{{ route('requests.update', $ringi->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">稟議書編集</h3>
                </div>
                <div class="card-body">
                    @include('requests.partials.form')
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-info shadow-sm">
                @include('requests.partials.attachments', [
                'ringi' => $ringi
                ])
            </div>
        </div>
    </div>
</form>
@endsection

@section('css')
<style>
    .custom-file-label::after {
        content: "参照" !important;
    }

    .custom-file-label {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding-right: 85px;
    }
</style>
@endsection

@section('js')
<script>
    // --- メインフォーム送信（下書き/申請） ---
    function submitMain(action) {
        const form = document.getElementById('mainForm');
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;

        form.appendChild(actionInput);
        form.submit();
    }

    // --- PDFプレビュー ---
    async function previewPdf() {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        [
            'ringi_type_id',
            'applied_date',
            'title',
            'content',
            'document_no'
        ].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) {
                formData.append(name, el.value);
            }
        });

        try {

            const response = await fetch(
                "{{ route('requests.pdf.preview') }}", {
                    method: 'POST',
                    body: formData
                }
            );

            if (!response.ok) {
                throw new Error('PDF生成失敗');
            }

            const blob = await response.blob();

            const pdfUrl = URL.createObjectURL(blob);

            Fancybox.show([{
                src: pdfUrl,
                type: "iframe"
            }]);

        } catch (e) {

            console.error(e);

            alert('PDFプレビューの生成に失敗しました');

        }
    }

    // --- 添付ファイル入力欄の追加 ---
    function addFileInput() {
        const list = document.getElementById('file-list');
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group shadow-sm mb-2';

        wrapper.innerHTML = `
            <div class="custom-file">
                <input type="file" name="files[]" class="custom-file-input">
                <label class="custom-file-label">
                    ファイルを選択...
                </label>
            </div>
            <div class="input-group-append">
                <button
                    type="button"
                    class="btn btn-outline-light"
                    onclick="removeFileInput(this)"
                >
                    <i class="fas fa-trash-alt" style="color:#343a40"></i>
                </button>
            </div>
        `;

        list.appendChild(wrapper);

        const input = wrapper.querySelector('.custom-file-input');
        input.addEventListener('change', function() {
            const label = this.nextElementSibling;
            if (this.files.length > 0) {
                label.innerText = this.files[0].name;
            }
        });
    }

    // --- 添付ファイル入力欄の削除 ---
    function removeFileInput(button) {
        button.closest('.input-group').remove();
    }
</script>
@endsection