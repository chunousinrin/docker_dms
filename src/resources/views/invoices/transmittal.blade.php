@extends('layouts.adminlte')

@section('title', '送付文書作成')

@section('content_header')
<div class="d-flex justify-content-between align-items-center px-2">
    <h1>送付文書作成</h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-main btn-sm shadow-sm px-3">
        <i class="fas fa-list mr-1"></i> 請求書一覧
    </a>
</div>
@stop

@section('content')
<form action="{{ route('invoices.transmittal_pdf') }}" method="POST" id="transmittal-form">
    @csrf
    <div class="row">
        {{-- 左側：基本設定 --}}
        <div class="col-md-7">
            <div class="card card-custom">
                <div class="card-body">

                    <div class="form-group row align-items-center">
                        <label class="font-weight-bold text-dark mb-0 small col-12 col-md-2 col-form-label-sm">発行日</label>
                        <div class="col-12 col-md-10">
                            <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm datepicker text-center">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="font-weight-bold text-dark mb-0 small col-12 col-md-2 col-form-label-sm">送付先</label>
                        <div class="col-12 col-md-10 input-group">
                            <input type="text" name="customer_name" value="{{ $customerName }}" class="form-control form-control-sm font-weight-bold col-10" placeholder="送付先名称">
                            <input type="text" name="honorific" value="{{ $honorific }}" class="form-control form-control-sm font-weight-bold col-2" placeholder="敬称">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="font-weight-bold text-dark mb-0 small col-12 col-md-2 col-form-label-sm">担当者</label>
                        <div class="col-12 col-md-10">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control bg-white" value="{{ $user->name }}" readonly>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="hidden" name="show_staff" value="0">
                                        <input type="checkbox" name="show_staff" value="1" checked id="switchStaff" title="表示する場合はチェック">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="font-weight-bold text-dark mb-0 small col-12 col-md-2 col-form-label-sm">タイトル</label>
                        <div class="col-12 col-md-10">
                            <input type="text" name="title" value="書類送付のご案内" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bold text-dark mb-0 small col-12 col-md-2 col-form-label-sm">頭語</label>
                        <div class="col-12 col-md-10">
                            <select class="form-control form-control-sm" name="greeting">
                                <option value="拝啓 / 敬具">拝啓 / 敬具</option>
                                <option value="謹啓 / 謹白">謹啓 / 謹白</option>
                                <option value="急啓 / 草々">急啓 / 草々</option>
                                <option value="拝復 / 敬具">拝復 / 敬具</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bold text-dark mt-2 mb-0 small col-12 col-md-2 col-form-label-sm">本文</label>
                        <div class="col-12 col-md-10">
                            <textarea name="body" class="form-control form-control-sm" rows="6">貴社におかれましては益々ご清栄のこととお慶び申し上げます。&#13;&#10;下記の通り、書類を送付いたしますのでご査収のほどよろしくお願い申し上げます。</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 右側：添付書類（Sortable.js対応） --}}
        <div class="col-md-5">
            <div class="card card-custom">
                <div class="card-header bg-white p-2 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold text-success">
                        <i class="fas fa-paperclip mr-1"></i> 添付書類（並び替え可）
                    </span>
                    <button type="button" id="add-row-btn" class="btn btn-xs btn-success ml-auto">
                        <i class="fas fa-plus mr-1"></i>行追加
                    </button>
                </div>

                {{-- ★ IDをご提示の js に合わせて「sortable-items」に変更 --}}
                <div class="card-body p-2" id="sortable-items">
                    {{-- 初期状態の1行目 --}}
                    <div class="d-flex align-items-center mb-2 p-2 bg-light border rounded item-row">
                        {{-- ★ handleに指定されている「.fa-bars」を配置 --}}
                        <div class="mr-2 text-muted" style="cursor: grab; width: 20px; text-align: center;">
                            <i class="fas fa-bars"></i>
                        </div>
                        <input type="text" name="attachments[0][name]" class="form-control form-control-sm mr-1" placeholder="書類名">
                        <input type="number" name="attachments[0][qty]" class="form-control form-control-sm mr-1 text-center input-qty" style="width: 70px;" placeholder="数量" value="1">
                        <input type="text" name="attachments[0][unit]" class="form-control form-control-sm mr-2 text-center" style="width: 60px;" placeholder="単位" value="部">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <div class="card-footer bg-white">
                    {{-- 7. PDF発行ボタン --}}
                    <button type="button" id="preview-btn" class="btn btn-sm btn-secondary btn-block mb-2 shadow-sm">
                        <i class="far fa-file-pdf mr-1"></i> PDFプレビュー
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
<style>
    :root {
        --main-color: #2d5a27;
    }

    .card-custom {
        border: 1px solid #e0e0e0;
        border-top: 3px solid var(--main-color);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .btn-main {
        background: var(--main-color);
        color: white !important;
        font-weight: bold;
    }

    .btn-main:hover {
        background: #23461e;
    }

    /* ご提示の js の ghostClass（並び替え中プレースホルダー）のスタイル設定 */
    .ui-state-highlight {
        border: 2px dashed #70bd29 !important;
        background: #f0f7f0 !important;
        opacity: 0.6;
    }
</style>
@endsection

@section('js')

{{-- 行操作・連動関数側の実装 (jQuery) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // インデックス更新用関数を window 経由で定義
        window.updateRowNumbers = function() {
            const rows = document.querySelectorAll('#sortable-items .item-row');

            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');

                inputs.forEach(input => {
                    let nameAttr = input.getAttribute('name');
                    if (nameAttr) {
                        // 各行の input の name 属性の添字を現在の並び順にリマッピング
                        let updatedName = nameAttr.replace(/\[\d+\]/, '[' + index + ']');
                        input.setAttribute('name', updatedName);
                    }
                });
            });
        };

        // 計算用関数
        window.calculate = function() {
            console.log("並び替え後の計算処理実行");
        };

        // 行追加ロジック
        const addRowBtn = document.getElementById('add-row-btn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', function() {
                // 現在の行数を取得して次のインデックスにする
                let nextIndex = document.querySelectorAll('#sortable-items .item-row').length;

                let newRow = `
                <div class="d-flex align-items-center mb-2 p-2 bg-light border rounded item-row">
                    <div class="mr-2 text-muted" style="cursor: grab; width: 20px; text-align: center;">
                        <i class="fas fa-bars"></i>
                    </div>
                    <input type="text" name="attachments[${nextIndex}][name]" class="form-control form-control-sm mr-1" placeholder="書類名">
                    <input type="number" name="attachments[${nextIndex}][qty]" class="form-control form-control-sm mr-1 text-center input-qty" style="width: 70px;" placeholder="数量" value="1">
                    <input type="text" name="attachments[${nextIndex}][unit]" class="form-control form-control-sm mr-2 text-center" style="width: 60px;" placeholder="単位" value="部">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fas fa-times"></i></button>
                </div>
            `;

                const sortableItems = document.getElementById('sortable-items');
                if (sortableItems) {
                    sortableItems.insertAdjacentHTML('beforeend', newRow);
                    window.updateRowNumbers(); // name属性のリセット
                    window.calculate();
                }
            });
        }

        // 行削除ロジック（イベントデリゲーション）
        document.addEventListener('click', function(e) {
            // クリックされた要素、またはその親に .remove-row-btn があるかチェック
            const removeBtn = e.target.closest('.remove-row-btn');

            if (removeBtn) {
                const rowsCount = document.querySelectorAll('#sortable-items .item-row').length;

                if (rowsCount > 1) {
                    const targetRow = removeBtn.closest('.item-row');
                    if (targetRow) {
                        targetRow.remove();
                        window.updateRowNumbers(); // 削除後も綺麗にインデックスを詰め直す
                        window.calculate();
                    }
                } else {
                    alert('少なくとも1行は必要です。');
                }
            }
        });

        $(function() {
            // ... 既存の日付ピッカーや行追加・削除のコード ...

            // ★ Fancyboxでフォーム送信結果のPDFを表示するロジックを追加
            $('#preview-btn').on('click', function(e) {
                let previewLoading = false;

                async function previewPdf() {
                    if (previewLoading) return;
                    previewLoading = true;

                    const $btn = $('#preview-btn');
                    const originalHtml = $btn.html();
                    $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> 生成中...').prop('disabled', true);

                    try {
                        const form = document.getElementById('transmittal-form');
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form), // @csrf の _token も含まれる
                            headers: {
                                'Accept': 'application/json'
                            } // 検証エラー時に302ではなく422を返させる
                        });

                        if (!response.ok) throw new Error('PDF生成に失敗しました');

                        const pdfUrl = URL.createObjectURL(await response.blob());
                        Fancybox.show([{
                            src: pdfUrl,
                            type: 'pdf'
                        }], {
                            on: {
                                destroy: () => URL.revokeObjectURL(pdfUrl)
                            }
                        });
                    } catch (e) {
                        console.error(e);
                        alert('PDFプレビューの生成に失敗しました。入力内容を確認してください。');
                    } finally {
                        previewLoading = false;
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                }

                $('#preview-btn').on('click', function(e) {
                    e.preventDefault();
                    previewPdf();
                });

                // Enterキーでの通常送信（ページ遷移）を防ぎ、プレビューに振り向ける
                $('#transmittal-form').on('submit', function(e) {
                    e.preventDefault();
                    previewPdf();
                });
            });
        });
    });
</script>
@endsection