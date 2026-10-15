<?php

namespace Database\Seeders;

use App\Models\StampItem;
use Illuminate\Database\Seeder;

class StampItemSeeder extends Seeder
{
    public function run(): void
    {
        // ※ 郵便料金・レターパック料金は改定されることがあります。額面は運用時点の値に合わせてください。
        $items = [
            // 収入印紙
            ['revenue_stamp', '200円',    200],
            ['revenue_stamp', '400円',    400],
            ['revenue_stamp', '500円',    500],
            ['revenue_stamp', '1,000円', 1000],
            ['revenue_stamp', '2,000円', 2000],
            ['revenue_stamp', '10,000円', 10000],

            // 郵便切手
            ['postage_stamp', '1円',   1],
            ['postage_stamp', '10円',   10],
            ['postage_stamp', '50円',   50],
            ['postage_stamp', '110円', 110],
            ['postage_stamp', '120円', 120],
            ['postage_stamp', '140円', 140],
            ['postage_stamp', '180円', 180],
            ['postage_stamp', '270円', 270],
            ['postage_stamp', '300円', 300],
            ['postage_stamp', '500円', 500],

            // レターパック
            ['letter_pack', 'レターパックライト', 430],
            ['letter_pack', 'レターパックプラス', 600],

            //その他
            ['other', 'その他', 0],
        ];

        foreach ($items as $i => [$category, $name, $face]) {
            StampItem::updateOrCreate(
                ['name' => $name],
                [
                    'category'   => $category,
                    'face_value' => $face,
                    'is_active'  => true,
                    'sort_order' => ($i + 1) * 10,
                ]
            );
        }
    }
}
