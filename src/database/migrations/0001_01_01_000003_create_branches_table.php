<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('支所ID');
            $table->string('name', 100)->comment('支所名');
            $table->string('representative', 100)->nullable()->comment('代表者名');
            $table->binary('seal_path')->nullable()->comment('印章ファイルパス');
            $table->string('postal_code', 10)->nullable()->comment('郵便番号');
            $table->string('address')->nullable()->comment('住所');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('fax', 20)->nullable()->comment('FAX番号');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE branches MODIFY seal_path MEDIUMBLOB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
