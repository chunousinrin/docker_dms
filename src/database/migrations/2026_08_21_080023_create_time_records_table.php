<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('work_date');                   // 勤務日
            $table->time('start_time')->nullable();       // 勤務開始時間
            $table->time('break_start_time')->nullable(); // 時間内退勤(休憩開始)
            $table->time('break_end_time')->nullable();   // 時間内出勤(休憩終了)
            $table->time('end_time')->nullable();          // 勤務終了時間
            $table->text('note')->nullable();              // 備考
            $table->timestamps();

            $table->unique(['user_id', 'work_date']); // 1人1日1レコード
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_records');
    }
};
