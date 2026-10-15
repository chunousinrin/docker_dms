<?php

namespace App\Http\Controllers\Forest;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Forest\ForestRecord;
use Illuminate\Support\Facades\{
    Schema,
    DB
};
use Symfony\Component\HttpFoundation\StreamedResponse;

class ForestSearchController extends Controller
{
    /**
     * 検索画面表示 & 検索実行
     */
    public function index(Request $request)
    {
        $results = null;
        $cities = [];
        $year = $request->input('年度');

        if ($year) {
            $tableName = "forest_{$year}";

            // 1. テーブル存在確認
            if (Schema::connection('forest_db')->hasTable($tableName)) {
                $query = ForestRecord::query()->from($tableName);

                // --- 2. 基本属性での絞り込み ---
                $this->applyBasicFilters($query, $request);

                // --- 3. 林班・準林班・小班・枝番の範囲検索 ---
                $this->applyRangeFilters($query, $request);

                // --- 4. 実行 (50件ペジネーション) ---
                $results = $query->paginate(50)->withQueryString();

                // --- 5. 初期表示用の市町村リスト取得 ---
                $cities = DB::connection('forest_db')
                    ->table($tableName)
                    ->distinct()
                    ->whereNotNull('市町村')
                    ->pluck('市町村');
            } else {
                session()->now('error', "{$year}年度のテーブルが見つかりません。");
            }
        }

        return view('work/sinrinbo/search_system', compact('results', 'cities'));
    }

    /**
     * JS連動用：選択肢を動的に取得するAPI
     */
    public function getOptions(Request $request)
    {
        $year = $request->year;
        $type = $request->type; // '大字' または '字'
        $tableName = "forest_{$year}";

        if (!Schema::connection('forest_db')->hasTable($tableName)) {
            return response()->json([]);
        }

        $query = DB::connection('forest_db')->table($tableName)->distinct();

        if ($type === '大字') {
            $query->where('市町村', $request->city)->whereNotNull('大字');
            $options = $query->pluck('大字');
        } elseif ($type === '字') {
            $query->where('市町村', $request->city)
                ->where('大字', $request->oaza)
                ->whereNotNull('字');
            $options = $query->pluck('字');
        }

        return response()->json($options);
    }

    /**
     * 基本フィルタリング
     */
    private function applyBasicFilters($query, $request)
    {
        if ($request->filled('市町村')) $query->where('市町村', $request->市町村);
        if ($request->filled('大字'))   $query->where('大字', $request->大字); // Select化したので完全一致
        if ($request->filled('字'))     $query->where('字', $request->字);     // Select化したので完全一致

        if ($request->filled('所有者')) {
            $s = $request->所有者;
            $query->where(function ($q) use ($s) {
                $q->where('現に所有者番号', 'like', "%{$s}%")
                    ->orWhere('林地所有者番号', 'like', "%{$s}%");
            });
        }
    }

    /**
     * 林班等の範囲検索ロジック
     */
    private function applyRangeFilters($query, $request)
    {
        $hasFrom = $request->filled('林班_from') || $request->filled('準林班_from') || $request->filled('小班_from') || $request->filled('枝番_from');
        $hasTo   = $request->filled('林班_to') || $request->filled('準林班_to') || $request->filled('小班_to') || $request->filled('枝番_to');

        if (!$hasFrom && !$hasTo) return;

        // パターンA: 林班指定がある場合 (sort_code利用)
        if ($request->filled('林班_from') || $request->filled('林班_to')) {
            $startCode = $this->generateSortCode(
                $request->input('林班_from', 0),
                $request->input('準林班_from'),
                $request->input('小班_from', 0),
                $request->input('枝番_from', 0)
            );

            $endCode = $this->generateSortCode(
                $request->input('林班_to', 999),
                $request->input('準林班_to'),
                $request->input('小班_to', 999),
                $request->input('枝番_to', 999),
                true
            );

            $query->whereBetween('sort_code', [$startCode, $endCode]);
        }
        // パターンB: 準林班以下の単独指定 (jun_code利用)
        else {
            if ($request->filled('準林班_from')) $query->where('jun_code', '>=', $this->convertJunToNum($request->準林班_from));
            if ($request->filled('準林班_to'))   $query->where('jun_code', '<=', $this->convertJunToNum($request->準林班_to));
            if ($request->filled('小班_from'))   $query->where('小班', '>=', $request->小班_from);
            if ($request->filled('小班_to'))     $query->where('小班', '<=', $request->小班_to);
        }
    }

    /**
     * 11桁ソートコード生成
     */
    private function generateSortCode($han, $jun, $sho, $eda, $isEnd = false)
    {
        $junNum = $this->convertJunToNum($jun);
        if ($isEnd && empty($jun)) $junNum = 99;
        return sprintf('%03d%02d%03d%03d', (int)$han, (int)$junNum, (int)$sho, (int)$eda);
    }

    /**
     * 準林班数値変換
     */
    private function convertJunToNum($char)
    {
        $map = [
            '' => 0,
            'イ' => 1,
            'ロ' => 2,
            'ハ' => 3,
            'ニ' => 4,
            'ホ' => 5,
            'ヘ' => 6,
            'ト' => 7,
            'チ' => 8,
            'リ' => 9,
            'ヌ' => 10,
            'ル' => 11,
            'ヲ' => 12,
            'ワ' => 13,
            'カ' => 14,
            'ヨ' => 15,
            'タ' => 16,
            'レ' => 17,
            'ソ' => 18,
            'ツ' => 19,
            'ネ' => 20,
            'ナ' => 21,
            'ラ' => 22,
            'ム' => 23
        ];
        return $map[$char] ?? 0;
    }

    public function export(Request $request)
    {
        $year = $request->input('年度');
        $tableName = "forest_{$year}";

        // 1. テーブル存在確認
        if (!Schema::connection('forest_db')->hasTable($tableName)) {
            return back()->with('error', "{$year}年度のテーブルが見つかりません。");
        }

        // 2. 出力するカラム名の取得（内部用コードを除外）
        $allColumns = Schema::connection('forest_db')->getColumnListing($tableName);

        $exclude = [
            'sort_code',
            'jun_code',
            'created_at',
            'updated_at',
            'id'
        ];

        $columns = array_values(array_diff($allColumns, $exclude));

        // 3. ファイル名の作成
        // 例: 森林簿_202603161110.csv
        $timestamp = date('YmdHi');
        $filename = "森林簿_{$timestamp}.csv";

        // 4. ストリームレスポンスの生成
        $response = new StreamedResponse(function () use (
            $request,
            $tableName,
            $columns
        ) {
            $handle = fopen('php://output', 'w');

            // Excelでの文字化け防止（UTF-8 BOM）
            fwrite($handle, "\xEF\xBB\xBF");

            /**
             * CSVの1行を作成する
             *
             * ・すべての項目を "..." で囲む
             * ・NULLは空文字にする
             * ・項目内の " は "" にエスケープ
             * ・改行コードはCRLF
             */
            $formatRow = function ($fields) {
                $escaped = array_map(function ($value) {

                    // NULLの場合は空文字
                    $val = $value === null ? '' : (string) $value;

                    // ダブルクォーテーションをCSV仕様に合わせてエスケープ
                    $val = str_replace('"', '""', $val);

                    // 必ずダブルクォーテーションで囲む
                    return '"' . $val . '"';
                }, $fields);

                // カンマ区切り + CRLF
                return implode(',', $escaped) . "\r\n";
            };

            // 5. ヘッダー行を書き込み
            fwrite($handle, $formatRow($columns));

            // 6. 検索条件を適用したクエリ
            $query = DB::connection('forest_db')->table($tableName);

            // 既存のフィルタリングメソッドを流用
            $this->applyBasicFilters($query, $request);
            $this->applyRangeFilters($query, $request);

            // 7. データを1000件ずつ取得して書き込み
            $query->orderBy('sort_code')->chunk(
                1000,
                function ($rows) use (
                    $handle,
                    $formatRow,
                    $columns
                ) {
                    foreach ($rows as $row) {

                        $record = [];

                        foreach ($columns as $column) {
                            $record[] = $row->{$column};
                        }

                        fwrite($handle, $formatRow($record));
                    }
                }
            );

            // 8. ファイルを閉じる
            fclose($handle);
        });

        // 9. レスポンスヘッダー
        $response->headers->set(
            'Content-Type',
            'text/csv; charset=UTF-8'
        );

        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . rawurlencode($filename) . '"'
        );

        return $response;
    }
}
