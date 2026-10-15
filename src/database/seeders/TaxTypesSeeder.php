<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tax_types')->insert([
            ['name' => '外税10%', 'type' => 'exclusive', 'rate' => 0.10, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '外税8%',  'type' => 'exclusive', 'rate' => 0.08, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '内税10%', 'type' => 'inclusive', 'rate' => 0.10, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '内税8%',  'type' => 'inclusive', 'rate' => 0.08, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '税抜',    'type' => 'none',     'rate' => 0.00, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
