<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->decimal('quantity', 20, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 20, 2)->nullable();
            // 並び順
            $table->integer('order_number')->default(0);
            $table->timestamps();
            $table->index(['estimate_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_items');
    }
};
