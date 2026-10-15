<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RingiTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // 現在日時を取得

        $ringiTypes = [
            [
                'id' => 1,
                'name' => '起案書',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => '復命書',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => '顛末書',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('ringi_types')->insert($ringiTypes);
    }
}
