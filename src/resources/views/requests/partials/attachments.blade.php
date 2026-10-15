<div class="card-header">
    <h3 class="card-title">
        <i class="fas fa-paperclip mr-2"></i>添付ファイル
    </h3>
</div>

<div class="card-body">
    <div id="file-list" class="mb-3">
        @if(isset($ringi))
        @foreach($ringi->currentAttachments as $file)
        <div class="border rounded p-2 mb-2 bg-light">
            <div>
                <a href="{{ Storage::url($file->file_path) }}" target="_blank">
                    <i class="fas fa-paperclip mr-1"></i>
                    {{ $file->file_name }}
                </a>
            </div>

            <div class="mt-1">
                <label class="mb-0">
                    <input type="checkbox" name="delete_files[]" value="{{ $file->id }}">
                    この添付を削除
                </label>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <button type="button" class="btn btn-outline-info btn-sm btn-block shadow-sm" onclick="addFileInput()">
        <i class="fas fa-plus-circle mr-1"></i>ファイルを追加
    </button>
</div>

<div class="card-footer bg-white border-top py-3">
    <button type="button" onclick="previewPdf()" class="btn btn-sm btn-secondary btn-block shadow-sm" title="PDFプレビュー">
        <i class="far fa-file-pdf mr-1"></i>PDFプレビュー
    </button>
    <button type="button" onclick="submitMain('draft')" class="btn btn-sm btn-light btn-block shadow-sm" title="下書き保存">
        <i class="fas fa-save mr-1"></i>下書き保存
    </button>
    <button type="button" onclick="submitMain('submit')" class="btn btn-sm btn-primary btn-block shadow-sm" title="申請">
        <i class="fas fa-file-alt mr-1"></i>申請 / 保存
    </button>
</div>