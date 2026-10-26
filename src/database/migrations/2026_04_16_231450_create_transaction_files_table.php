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
        Schema::create('transaction_files', function (Blueprint $table) {
            $table->id();
            // ③の変更を統合: cascade → restrict(取引を消してもファイル記録が連鎖削除されないように)
            $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
            $table->string('file_type'); // 請求書, 明細書, 納品書等
            $table->string('file_path'); // OneDrive同期フォルダ内の相対パス
            $table->string('original_filename'); // 元のファイル名
            $table->string('file_hash')->nullable()->index(); // SHA-256等のハッシュ値
            // ③の変更を統合
            $table->unsignedBigInteger('file_size')->nullable(); // バイト数
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_files');
    }
};
