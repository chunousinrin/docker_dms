<?php

declare(strict_types=1);

namespace App\Http\Requests\AttendanceV2;

use App\Models\WorkerV2\WorkerV2;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function worker(): WorkerV2
    {
        return WorkerV2::query()
            ->where('qr_token', (string) $this->route('token'))
            ->where('is_active', true)
            ->firstOrFail();
    }
}
