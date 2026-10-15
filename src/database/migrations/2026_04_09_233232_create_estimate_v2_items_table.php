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
        Schema::create('estimate_v2_items', function (Blueprint $table) {
            $table->id();
            // 親が消えたら子も消える設定
            $table->foreignId('estimate_v2_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order'); // 1〜12行の並び順
            $table->string('summary')->nullable(); // 全角スペースが含まれる項目
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit', 50)->nullable();
            $table->integer('unit_price')->default(0);
            $table->integer('amount')->default(0);
            $table->string('remark', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_v2_items');
    }
};
