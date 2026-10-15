<?php

namespace App\Models\Shared;

use App\Models\Master\User;
use App\Models\Shared\DailyReportBusinessType;
use App\Models\Shared\DailyReportWeather;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_date',
        'user_id',
        'am_business_type_id',
        'am_summary',
        'am_weather_id',
        'pm_business_type_id',
        'pm_summary',
        'pm_weather_id',
        'remarks',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function amBusinessType(): BelongsTo
    {
        return $this->belongsTo(DailyReportBusinessType::class, 'am_business_type_id');
    }

    public function pmBusinessType(): BelongsTo
    {
        return $this->belongsTo(DailyReportBusinessType::class, 'pm_business_type_id');
    }

    public function amWeather(): BelongsTo
    {
        return $this->belongsTo(DailyReportWeather::class, 'am_weather_id');
    }

    public function pmWeather(): BelongsTo
    {
        return $this->belongsTo(DailyReportWeather::class, 'pm_weather_id');
    }
}
