<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Accounting\Estimate;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 全職員共通利用のため、ここでは権限チェックしない
    }

    public function rules(): array
    {
        return [
            // 論理削除済みの番号も重複扱いになる（欠番の再利用を防ぐ）
            'invoice_number'        => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('invoices', 'invoice_number')],
            'invoice_category_id'   => ['required', Rule::exists('classifications', 'id')],
            'billing_date'          => ['required', 'date_format:Y-m-d'],
            'due_date'              => ['nullable', 'date_format:Y-m-d', 'after_or_equal:billing_date'],
            'completion_start_date' => ['nullable', 'date_format:Y-m-d'],
            'completion_end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:completion_start_date'],

            'customer_name'         => ['required', 'string', 'max:100'],
            'honorific'             => ['nullable', 'string', 'max:10'],
            'project_id'            => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'project_name'          => ['nullable', 'string', 'max:200'],
            'project_location'      => ['nullable', 'string', 'max:200'],
            'estimate_id'           => ['nullable', 'integer', Rule::exists(Estimate::class, 'id')],

            'requester_id'          => ['required', Rule::exists('branches', 'id')],
            'tax_type_id'           => ['required', Rule::exists('tax_types', 'id')->where('active', 1)],
            'bank_id1'              => ['nullable', Rule::exists('banks', 'id')],
            'bank_id2'              => ['nullable', Rule::exists('banks', 'id')],
            'bank_id3'              => ['nullable', Rule::exists('banks', 'id')],

            'show_creator'          => ['nullable', 'boolean'],
            'show_billing_date'     => ['nullable', 'boolean'],
            'remarks'               => ['nullable', 'string', 'max:1000'],
            'memo'                  => ['nullable', 'string', 'max:2000'],

            'items'                 => ['required', 'array', 'max:15'],
            'items.*.description'   => ['nullable', 'string', 'max:200'],
            'items.*.quantity'      => ['nullable', 'numeric', 'between:-99999999,99999999'], // 値引き行を想定しマイナス可
            'items.*.unit'          => ['nullable', 'string', 'max:10'],
            'items.*.unit_price'    => ['nullable', 'numeric', 'between:-9999999999,9999999999'],
            'items.*.order_number'  => ['nullable', 'integer', 'between:1,15'],
        ];
    }

    public function attributes(): array
    {
        return [
            'invoice_number'      => '請求番号',
            'invoice_category_id' => '分類',
            'billing_date'        => '発行日',
            'due_date'            => '支払期限',
            'completion_start_date' => '完了期間（開始）',
            'completion_end_date' => '完了期間（終了）',
            'customer_name'       => '顧客名',
            'requester_id'        => '発行者',
            'tax_type_id'         => '税率設定',
            'estimate_id'         => '見積書',
            'items.*.description' => '品名',
            'items.*.quantity'    => '数量',
            'items.*.unit_price'  => '単価',
        ];
    }

    public function withValidator($validator): void
    {
        // 明細が1行も入力されていない請求書を弾く（不要なら削除）
        $validator->after(function ($v) {
            $hasRow = collect($this->input('items', []))->contains(
                fn($i) =>
                filled($i['description'] ?? null) || filled($i['quantity'] ?? null) || filled($i['unit_price'] ?? null)
            );
            if (! $hasRow) {
                $v->errors()->add('items', '明細を1行以上入力してください。');
            }
        });
    }
}
