<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('request_id')->constrained('ringi_requests')->onDelete('cascade');

            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();

            $table->integer('step_order');

            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['request_id', 'step_order']);
            $table->index('approver_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
