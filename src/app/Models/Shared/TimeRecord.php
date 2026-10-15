<?php

namespace App\Models\Shared;

use App\Models\Master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

    protected $table = 'time_records';

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'break_start_time',
        'break_end_time',
        'end_time',
        'note',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 現在の状態で打刻可能な項目(type)の一覧を返す
     */
    public function allowedPunchTypes(): array
    {
        if ($this->end_time) {
            return []; // 全て完了。押せる項目なし
        }

        if ($this->break_end_time) {
            return ['end_time'];
        }

        if ($this->break_start_time) {
            return ['break_end_time'];
        }

        if ($this->start_time) {
            return ['break_start_time', 'end_time'];
        }

        return ['start_time'];
    }

    /**
     * 指定のtypeが現在押せるかどうか
     */
    public function canPunch(string $type): bool
    {
        return in_array($type, $this->allowedPunchTypes(), true);
    }
}
