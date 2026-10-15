<?php

namespace App\Models\Forest;

use Illuminate\Database\Eloquent\Model;

class ForestRecord extends Model
{
    protected $connection = 'forest_db';
    protected $primaryKey = 'SID';
    public $timestamps = false;

    /**
     * 動的にテーブルを設定するカスタムメソッド
     */
    public function scopeInYear($query, $year)
    {
        // 例: 年度2024を受け取ったらテーブル名を "forest_2024" に設定
        return $query->from("forest_{$year}");
    }
    // App\Models\ForestRecord.php

    public function scopeWhereRangeCode($query, $fromArray, $toArray)
    {
        // 各値を 0011001000 の形式に変換する補助関数
        $generateCode = function ($arr) {
            $han = str_pad($arr['林班'] ?? 0, 3, '0', STR_PAD_LEFT);
            $jun = $arr['準林班'] ?? 0; // ここは数値に変換済みの前提
            $sho = str_pad($arr['小班'] ?? 0, 3, '0', STR_PAD_LEFT);
            $eda = str_pad($arr['枝番'] ?? 0, 3, '0', STR_PAD_LEFT);
            return $han . $jun . $sho . $eda;
        };

        $start = $generateCode($fromArray);
        $end   = $generateCode($toArray);

        return $query->whereBetween('sort_code', [$start, $end]);
    }
}
