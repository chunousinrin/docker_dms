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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_category_id');
            $table->foreignId('creator_id');
            $table->date('billing_date');
            $table->foreignId('requester_id'); // 発行者(支所)
            $table->string('invoice_number')->unique();
            $table->foreignId('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('customer_name');
            $table->string('honorific')->default('様');
            $table->decimal('tax_amount', 20, 2)->default(0); // 消費税額
            $table->decimal('total_billing', 20, 2)->default(0); // 税込合計
            $table->string('project_location')->nullable();
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignId('tax_type_id'); // 税率マスタ参照
            $table->text('remarks')->nullable();
            $table->text('memo')->nullable();
            $table->boolean('show_creator')->default(true);
            $table->boolean('show_billing_date')->default(true);
            $table->foreignId('bank_id1')->nullable();
            $table->foreignId('bank_id2')->nullable();
            $table->foreignId('bank_id3')->nullable();
            $table->date('completion_start_date')->nullable();
            $table->date('completion_end_date')->nullable();
            $table->timestamps();
            $table->foreignId('estimate_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
