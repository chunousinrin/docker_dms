<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_group_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers_v2')
                ->cascadeOnDelete();

            $table->foreignId('group_id')
                ->constrained('workergroups_v2')
                ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->timestamps();

            // インデックス（検索高速化）
            $table->index(['worker_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_group_histories');
    }
};
