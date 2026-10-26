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

    {{-- バリデーションエラー --}}
    @if ($errors->any())
    <div class="alert alert-danger small py-2">
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="create-form">
        @csrf

        <div class="row">
            {{-- ① 取引先 --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">取引先名 <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-control" list="customer-list" placeholder="取引先名を入力または選択" required maxlength="255" autocomplete="off">
                <datalist id="customer-list">
                    @foreach($customers as $name)
                    <option value="{{ $name }}">
                    @endforeach
                </datalist>
            </div>

            {{-- ② 取引年月日 --}}
            <div class="form-group col-md-6">
                <label class="font-weight-bold text-dark mb-1 small">取引年月日 <span class="text-danger">*</span></label>
                <input type="text" name="transaction_date" value="{{ old('transaction_date') }}" class="form-control datepicker" required>
            </div>

            {{-- ③ 取引金額 --}}
            <div class="form-group col-md-6">
                <label class="font-weight-bold text-dark mb-1 small">取引金額 <span class="text-danger">*</span></label>
                <input type="number" name="amount" value="{{ old('amount') }}" class="form-control" placeholder="金額を数字で入力" required min="0" step="1">
            </div>

            {{-- ④ 備考 --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">備考</label>
                <textarea name="note" class="form-control" rows="2" maxlength="2000" placeholder="メモなどがあれば入力">{{ old('note') }}</textarea>
            </div>

            {{-- ⑤ 証跡書類 --}}
            <div class="form-group col-md-12">
                <label class="font-weight-bold text-dark mb-1 small">証跡書類添付 <span class="text-danger">*</span>
                    <span class="text-muted font-weight-normal">（PDF / JPG / PNG、1ファイル20MBまで）</span>
                </label>

                <div id="file-inputs-container">
                    <div class="row no-gutters mb-1 file-input-row">
                        <div class="input-group">
                            <select name="documents[0][file_type]" class="form-control col-4" required>
                                <option value="" selected hidden>種類を選択</option>
                                @foreach($fileTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>

                            <div class="custom-file">
                                <input type="file" name="documents[0][file]" class="custom-file-input" accept="application/pdf,image/jpeg,image/png" required>
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

                <div class="text-left mt-1">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="btn-add-file">
                        <i class="fas fa-plus mr-1"></i> 書類を追加
                    </button>
                </div>
            </div>

            <div class="mt-4 text-right border-top pt-3 col-12">
                <button type="submit" id="btn-submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                    <i class="fas fa-save mr-1"></i> この内容で保存する
                </button>
            </div>
        </div>
    </form>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>
        $(function() {
            const fileTypes = @js($fileTypes);
            let fileIndex = 1;

            const typeOptions = fileTypes
                .map(t => $('<option>').val(t).text(t)[0].outerHTML)
                .join('');

            // 1. 書類行の追加
            $('#btn-add-file').click(function() {
                const newRow = `
                <div class="row no-gutters mb-1 file-input-row">
                    <div class="input-group">
                        <select name="documents[${fileIndex}][file_type]" class="form-control col-4" required>
                            <option value="" selected hidden>種類を選択</option>
                            ${typeOptions}
                        </select>
                        <div class="custom-file">
                            <input type="file" name="documents[${fileIndex}][file]" class="custom-file-input" accept="application/pdf,image/jpeg,image/png" required>
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

            // 2. 行の削除（最低1行は残す）
            $(document).on('click', '.btn-remove-file', function() {
                if ($('.file-input-row').length > 1) {
                    $(this).closest('.file-input-row').remove();
                } else {
                    alert('最低1つのファイルを添付してください。');
                }
            });

            // 3. 選択したファイル名の表示
            $(document).on('change', '.custom-file-input', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass('selected').text(fileName || '添付ファイル');
            });

            // 4. 二重送信の防止
            $('#create-form').on('submit', function() {
                $('#btn-submit').prop('disabled', true);
            });
        });
    </script>
</body>

</html>
