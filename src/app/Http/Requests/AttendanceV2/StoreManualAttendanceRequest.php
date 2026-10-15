<?php

declare(strict_types=1);

namespace App\Http\Requests\AttendanceV2;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view-reports') ?? false;
    }

    public function rules(): array
    {
        return [
            'work_date' => ['required', 'date'],
            'group_id' => ['required', 'integer', 'exists:workergroups_v2,id'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
