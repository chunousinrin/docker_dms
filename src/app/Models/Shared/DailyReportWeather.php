<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReportWeather extends Model
{
    use HasFactory;

    protected $table = 'daily_report_weathers';

    protected $fillable = [
        'name',
    ];

    public function dailyReportsAsAm(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'am_weather_id');
    }

    public function dailyReportsAsPm(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'pm_weather_id');
    }
}
