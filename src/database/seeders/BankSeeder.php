<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリアする場合（必要に応じてコメントアウトしてください）
        // DB::table('banks')->truncate();

        $banks = [
            [
                'id' => 1,
                'bank_name' => '十六銀行',
                'branch_name' => '美濃支店',
                'account_type' => '普通',
                'account_number' => '1361712',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '美濃支店',
                'account_type' => '普通',
                'account_number' => '9010505',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '下牧支店',
                'account_type' => '普通',
                'account_number' => '9005315',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '洞戸支店',
                'account_type' => '普通',
                'account_number' => '9012109',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '津保川支店',
                'account_type' => '普通',
                'account_number' => '9482105',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '上之保支店',
                'account_type' => '普通',
                'account_number' => '9706101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '上之保支店',
                'account_type' => '普通',
                'account_number' => '0012477',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'bank_name' => 'めぐみの農協',
                'branch_name' => '武芸川支店',
                'account_type' => '普通',
                'account_number' => '9035303',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // タイムスタンプ（created_at, updated_at）が必要な場合は追加してください
        DB::table('banks')->insert($banks);
    }
}
