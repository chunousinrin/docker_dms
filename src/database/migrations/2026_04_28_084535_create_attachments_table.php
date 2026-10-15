<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('request_id')->constrained('ringi_requests')->onDelete('cascade');

            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();

            $table->integer('version');
            $table->boolean('is_current')->default(true);

            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            $table->timestamp('replaced_at')->nullable();

            $table->timestamps();

            $table->index('request_id');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
