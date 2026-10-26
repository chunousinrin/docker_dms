<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\TransactionFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証は auth ミドルウェアで済んでいる
    }

    protected function prepareForValidation(): void
    {
        // 取引先名の前後・連続空白（全角含む）を正規化
        if (is_string($this->customer_name)) {
            $this->merge(['customer_name' => preg_replace('/[\s\x{3000}]+/u', ' ', trim($this->customer_name))]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_name'          => ['required', 'string', 'max:255'],
            'transaction_date'       => ['required', 'date'],
            // decimal(15,0) に合わせて整数のみ。値引・返品など負数を扱う場合は min を外す
            'amount'                 => ['required', 'integer', 'between:0,999999999999999'],
            'note'                   => ['nullable', 'string', 'max:2000'],

            'documents'              => ['required', 'array', 'min:1', 'max:20'],
            'documents.*.file_type'  => ['required', 'string', Rule::in(TransactionFile::TYPES)],
            // mimes は拡張子ではなく実ファイルの内容から MIME を判定する。20MB 上限(KB単位)
            'documents.*.file'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name'         => '取引先名',
            'transaction_date'      => '取引年月日',
            'amount'                => '取引金額',
            'note'                  => '備考',
            'documents'             => '証跡書類',
            'documents.*.file_type' => '書類の種類',
            'documents.*.file'      => '添付ファイル',
        ];
    }
}
