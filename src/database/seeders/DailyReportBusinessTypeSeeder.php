<?php

namespace Database\Seeders;

use App\Models\Shared\DailyReportBusinessType;
use Illuminate\Database\Seeder;

class DailyReportBusinessTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            '一般事務',
            '造林事務',
            '造林現場',
            '林産事務',
            '林産現場',
            '作業路業務',
            '生産性強化',
            '環境税間伐',
            '里山整備事業',
            '境界明確化',
            '支援交付金',
            '緑の雇用',
            '共販業務',
            '森水業務',
            '販売業務',
            '加工業務',
            '購買業務',
            'イベント業務',
            '会議・打合せ',
            '有・特休暇',
            '代休',
            '欠勤',
            'その他'
        ];

        foreach ($types as $type) {
            DailyReportBusinessType::firstOrCreate(['name' => $type]);
        }
    }
}
