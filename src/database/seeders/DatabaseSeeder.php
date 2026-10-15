<?php

namespace Database\Seeders;

use App\Models\Master\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            //'name' => 'Test User',
            // 'email' => 'test@example.com',
        ]);

        $this->call([
            DailyReportBusinessTypeSeeder::class,
            DailyReportWeatherSeeder::class,
            TaxTypesSeeder::class,
            BankSeeder::class,
            ClassificationSeeder::class,
            RingiTypeSeeder::class,
            StampItemSeeder::class,
        ]);
    }
}
