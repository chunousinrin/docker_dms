<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances_v2', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers_v2')
                ->cascadeOnDelete();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained('workergroups_v2')
                ->nullOnDelete();

            $table->unsignedBigInteger('site_id');

            $table->date('work_date');

            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();

            $table->text('comment')->nullable();

            $table->timestamps();

            // 1日1レコード制約（超重要）
            $table->unique(['worker_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances_v2');
    }
};
