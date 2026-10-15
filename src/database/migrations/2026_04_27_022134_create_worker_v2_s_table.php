<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers_v2', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->uuid('qr_token')->unique();
            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers_v2');
    }
};
