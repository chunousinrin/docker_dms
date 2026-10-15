<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table) {

            $table->id();
            // 区分
            $table->foreignId('estimate_category_id')->nullable()->constrained('classifications');
            // 作成者
            $table->foreignId('creator_id')->constrained('users');
            // 依頼部署
            $table->foreignId('requester_id')->nullable()->constrained('branches');
            // 税区分
            $table->foreignId('tax_type_id')->constrained('tax_types');
            $table->date('created_date')->nullable();
            $table->string('estimate_number', 50)->unique();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('honorific')->nullable();
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total_estimate', 20, 2)->default(0);
            $table->string('project_location')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->text('remarks')->nullable();
            $table->text('memo')->nullable();
            // 表示設定
            $table->boolean('show_creator')->default(true);
            $table->boolean('show_estimate_date')->default(true);
            $table->boolean('unit_price_estimate')->default(true);
            $table->timestamps();
            // index
            $table->index('created_date');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
