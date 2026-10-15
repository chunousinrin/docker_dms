<?php

namespace Database\Seeders;

use App\Models\Shared\DailyReportWeather;
use Illuminate\Database\Seeder;

class DailyReportWeatherSeeder extends Seeder
{
    public function run(): void
    {
        $weathers = ['晴', '曇', '雨', '雷', '雪', 'その他'];

        foreach ($weathers as $weather) {
            DailyReportWeather::firstOrCreate(['name' => $weather]);
        }
    }
}
