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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained(); // 取引先
            $table->date('transaction_date'); // 取引年月日（検索要件1）
            $table->decimal('amount', 15, 0); // 取引金額（検索要件2）
            $table->string('customer_name'); // 検索要件3：保存時の名称（マスタ変更対策）
            $table->text('note')->nullable(); // 備考
            $table->timestamps();

            // 検索パフォーマンス向上のためのインデックス
            $table->index(['transaction_date', 'amount', 'customer_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
