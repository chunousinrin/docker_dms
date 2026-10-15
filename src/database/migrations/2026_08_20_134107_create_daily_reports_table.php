<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_report_business_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('業種名');
            $table->timestamps();
        });

        Schema::create('daily_report_weathers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('天気名');
            $table->timestamps();
        });

        Schema::create(
            'daily_reports',
            function (Blueprint $table) {
                $table->id();

                $table->date('work_date')->comment('勤務日');

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete()
                    ->comment('ユーザーID');

                // 午前
                $table->foreignId('am_business_type_id')
                    ->nullable()
                    ->constrained('daily_report_business_types')
                    ->nullOnDelete()
                    ->comment('午前業種');
                $table->text('am_summary')->nullable()->comment('午前摘要');
                $table->foreignId('am_weather_id')
                    ->nullable()
                    ->constrained('daily_report_weathers')
                    ->nullOnDelete()
                    ->comment('午前天気');

                // 午後
                $table->foreignId('pm_business_type_id')
                    ->nullable()
                    ->constrained('daily_report_business_types')
                    ->nullOnDelete()
                    ->comment('午後業種');
                $table->text('pm_summary')->nullable()->comment('午後摘要');
                $table->foreignId('pm_weather_id')
                    ->nullable()
                    ->constrained('daily_report_weathers')
                    ->nullOnDelete()
                    ->comment('午後天気');

                $table->text('remarks')->nullable()->comment('備考');

                $table->timestamps();

                // 1ユーザー・1勤務日につき1件のみ登録可能にする制約（本人が1日1回だけ入力できる、の要）
                $table->unique(['user_id', 'work_date']);
            }
        );
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');

        Schema::dropIfExists('daily_report_weathers');

        Schema::dropIfExists('daily_report_business_types');
    }
};
