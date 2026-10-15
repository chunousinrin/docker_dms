<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // 基本情報
            $table->date('order_date')->comment('発注日');
            $table->string('order_number')->unique()->comment('発注番号');

            // 発注先
            $table->string('supplier_name');
            $table->string('supplier_title')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_department')->nullable();
            $table->string('supplier_person')->nullable();
            $table->string('supplier_person_title')->nullable();

            // 発行者
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // 担当者
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // 税率
            $table->foreignId('tax_type_id')
                ->constrained('tax_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // 金額
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            // 備考
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('order_date');
            $table->index('branch_id');
            $table->index('user_id');
        });
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('order_number');

            $table->string('item_name')->nullable();

            $table->decimal('quantity', 10, 2)->nullable();

            $table->string('unit', 20)->nullable();

            $table->decimal('unit_price', 15, 2)->nullable();

            $table->decimal('amount', 15, 2)->default(0);

            $table->string('remarks')->nullable();

            $table->timestamps();

            $table->index(['purchase_order_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
