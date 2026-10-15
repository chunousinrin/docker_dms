<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_date' => [
                'required',
                'date',
                // ログインユーザー本人について、同じ勤務日の日報は1件のみ許可する
                Rule::unique('daily_reports', 'work_date')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'am_business_type_id' => ['nullable', 'exists:daily_report_business_types,id'],
            'am_summary' => ['nullable', 'string'],
            'am_weather_id' => ['nullable', 'exists:daily_report_weathers,id'],
            'pm_business_type_id' => ['nullable', 'exists:daily_report_business_types,id'],
            'pm_summary' => ['nullable', 'string'],
            'pm_weather_id' => ['nullable', 'exists:daily_report_weathers,id'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'work_date.unique' => 'その勤務日の日報は既に登録されています。',
        ];
    }

    public function attributes(): array
    {
        return [
            'work_date' => '勤務日',
            'am_business_type_id' => '午前業種',
            'am_summary' => '午前摘要',
            'am_weather_id' => '午前天気',
            'pm_business_type_id' => '午後業種',
            'pm_summary' => '午後摘要',
            'pm_weather_id' => '午後天気',
            'remarks' => '備考',
        ];
    }
}
