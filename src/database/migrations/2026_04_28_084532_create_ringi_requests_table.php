<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ringi_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ringi_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_no')->unique();

            $table->string('title');
            $table->text('content')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('pending'); // pending / approved / rejected
            $table->date('applied_date')->nullable();

            $table->unsignedInteger('current_step')->nullable();

            $table->timestamp('last_file_updated_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ringi_requests');
    }
};
