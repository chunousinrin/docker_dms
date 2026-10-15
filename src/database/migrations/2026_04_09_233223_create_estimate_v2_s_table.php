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
        Schema::create('estimate_v2s', function (Blueprint $table) {
            $table->id(); // Es2IDに相当
            $table->integer('classification_id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('staff_display')->default(0); // Es2StaffDisplay
            $table->string('estimate_number')->unique();
            $table->date('created_date');
            $table->integer('cd_display')->default(0);
            $table->integer('branch_id');
            $table->string('customer_name', 50);
            $table->string('honorific', 10)->nullable();
            $table->string('biz_name', 50)->nullable();
            $table->string('location', 50)->nullable();
            $table->string('works_type', 50)->nullable();
            $table->date('works_period_start')->nullable();
            $table->date('works_period_end')->nullable();
            $table->date('effective_date')->nullable();
            $table->integer('total_unit_price')->default(0); // Es2UnitPrice
            $table->decimal('tax_rate', 10, 2)->default(0); // Es2TaxRate
            $table->text('note')->nullable(); // NB
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_v2s');
    }
};
