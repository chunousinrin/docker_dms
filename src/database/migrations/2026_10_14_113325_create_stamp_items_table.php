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
        // 1. stamp_items
        Schema::create('stamp_items', function (Blueprint $t) {
            $t->id();
            $t->string('category');             // revenue_stamp / postage_stamp / letter_pack / other
            $t->string('name');                 // 例: 収入印紙 200円
            $t->unsignedInteger('face_value');  // 額面（円）
            $t->boolean('is_active')->default(true);
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique('name');
        });
        // 2. inventory_audits
        Schema::create('inventory_audits', function (Blueprint $t) {
            $t->id();
            $t->date('audited_on');
            $t->foreignId('auditor_id')->constrained('users');
            $t->string('memo')->nullable();
            $t->timestamps();
            $t->index('audited_on');
        });
        // 3. stamp_transactions
        Schema::create('stamp_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stamp_item_id')->constrained();
            $t->string('type');                         // in / out / adjust
            $t->integer('quantity');                    // 符号付き（in:+ / out:- / adjust:±）
            $t->date('transacted_on');
            $t->string('counterparty')->nullable();     // 購入先 / 使用先・用途
            $t->string('memo')->nullable();
            $t->foreignId('user_id')->constrained();    // 記録者
            $t->foreignId('audit_id')->nullable()->constrained('inventory_audits'); // 棚卸調整由来
            $t->timestamps();
            $t->index(['stamp_item_id', 'transacted_on']);
            $t->index('type');
        });
        // 4. inventory_audit_lines
        Schema::create('inventory_audit_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('inventory_audit_id')->constrained()->cascadeOnDelete();
            $t->foreignId('stamp_item_id')->constrained();
            $t->integer('book_qty');       // 帳簿上の数量
            $t->integer('counted_qty');    // 実数
            $t->integer('diff');           // counted - book
            $t->unsignedInteger('face_value'); // 監査時点の額面（金額集計用スナップショット）
            $t->string('note')->nullable();
            $t->unique(['inventory_audit_id', 'stamp_item_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_lines');
        Schema::dropIfExists('stamp_transactions');
        Schema::dropIfExists('inventory_audits');
        Schema::dropIfExists('stamp_items');
    }
};
