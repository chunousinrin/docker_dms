<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            padding: 20px;
        }

        .custom-file-label::after {
            content: "ファイルを選択";
        }
    </style>

</head>

<body>

    <div class="pb-2 mb-3 border-bottom">
        <h5 class="m-0 font-weight-bold" style="color: var(--main-color);">
            <i class="fas fa-plus-circle mr-2"></i> 電子帳簿 証跡新規登録
        </h5>
    </div>

    {{-- 保存処理自体は、元々用意してある通常の store ルートへPOSTします --}}
    <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="create-form">
        @csrf

        <div class="row">
            {{-- ① 取引先入力（datalistによる重複排除補完） --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">取引先名 <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control" list="customer-list" placeholder="取引先名を入力または選択" required autocomplete="off">

                <datalist id="customer-list">
                    @foreach(\App\Models\Master\Customer::select('name')->groupBy('name')->orderBy('name', 'asc')->get() as $customer)
                    <option value="{{ $customer->name }}">
                        @endforeach
                </datalist>
            </div>

            {{-- ② 取引年月日 --}}
            <div class="form-group col-md-6">
                <label class="font-weight-bold text-dark mb-1 small">取引年月日 <span class="text-danger">*</span></label>
                <input type="text" name="transaction_date" class="form-control datepicker" required>
            </div>

            {{-- ③ 取引金額 --}}
            <div class="form-group col-md-6">
                <label class="font-weight-bold text-dark mb-1 small">取引金額 <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" placeholder="金額を数字で入力" required>
            </div>

            {{-- ④ 備考 --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">備考</label>
                <textarea name="note" class="form-control" rows="2" placeholder="メモなどがあれば入力"></textarea>
            </div>

            {{-- ⑤ 証跡書類アップロードエリア（動的追加用） --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">証跡書類添付 <span class="text-danger">*</span></label>

                {{-- ファイル行が追加されていくコンテナ --}}
                <div id="file-inputs-container">

                    {{-- 1行目（最初から削除ボタン付き） --}}
                    <div class="row no-gutters mb-1 file-input-row">
                        <div class="input-group">
                            <select name="documents[0][file_type]" class="form-control col-4" required>
                                <option value="" selected hidden>種類を選択</option>
                                <option value="請求書">請求書</option>
                                <option value="領収書">領収書</option>
                                <option value="注文書">注文書</option>
                                <option value="その他">その他</option>
                            </select>

                            <div class="custom-file">
                                <input type="file" name="documents[0][file]" class="custom-file-input" accept="application/pdf,image/*" required>
                                <label class="custom-file-label">添付ファイル</label>
                            </div>

                            <div class="input-group-append">
                                <button class="btn btn-danger btn-remove-file" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 追加ボタンは外側（下部）に1つだけ配置 --}}
                <div class="text-left mt-1">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="btn-add-file">
                        <i class="fas fa-plus mr-1"></i> 書類を追加
                    </button>
                </div>
            </div>
            {{-- アクションボタン --}}
            <div class="mt-4 text-right border-top pt-3 col-12">
                <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                    <i class="fas fa-save mr-1"></i> この内容で保存する
                </button>
            </div>
        </div>
    </form>
    {{-- jQueryと、書類の動的追加JS --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>
        $(function() {
            let fileIndex = 1;

            // 1. 外側の「書類を追加」ボタンを押したときの挙動
            $('#btn-add-file').click(function() {
                const newRow = `
            <div class="row no-gutters mb-1 file-input-row">
                <div class="input-group">
                    <select name="documents[${fileIndex}][file_type]" class="form-control col-4" required>
                        <option value="" selected hidden>種類を選択</option>
                        <option value="請求書">請求書</option>
                        <option value="領収書">領収書</option>
                        <option value="注文書">注文書</option>
                        <option value="その他">その他</option>
                    </select>

                    <div class="custom-file">
                        <input type="file" name="documents[${fileIndex}][file]" class="custom-file-input" accept="application/pdf,image/*" required>
                        <label class="custom-file-label">添付ファイル</label>
                    </div>
                    
                    <div class="input-group-append">
                        <button class="btn btn-danger btn-remove-file" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>`;

                $('#file-inputs-container').append(newRow);
                fileIndex++;
            });

            // 2. 削除ボタンを押したときの挙動
            $(document).on('click', '.btn-remove-file', function() {
                // 電帳法上の必須要件として最低1行は残したいため、最後の1行は消せないように制御
                if ($('.file-input-row').length > 1) {
                    $(this).closest('.file-input-row').remove();
                } else {
                    alert('最低1つのファイルを添付してください。');
                }
            });

            // 3. ファイル名書き換え処理
            $(document).on('change', '.custom-file-input', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || "添付ファイル");
            });
        });
    </script>
</body>

</html>