<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('request_id')->constrained('ringi_requests')->onDelete('cascade');
            $table->unsignedBigInteger('step_id')->nullable();

            $table->string('action'); // approved / rejected
            $table->text('comment')->nullable();

            $table->foreignId('approved_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('approved_at');

            $table->integer('file_version_at_approval')->nullable();

            $table->timestamps();

            $table->index('request_id');
            $table->index('step_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_histories');
    }
};
