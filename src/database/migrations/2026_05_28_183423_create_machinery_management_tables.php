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
        Schema::create('machinery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->timestamps();
        });
        Schema::create('machinery_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('machinery_categories')->nullOnDelete();
            $table->string('machine_name');
            $table->enum('status', ['active', 'maintenance', 'repair', 'retired', 'return'])->default('active');
            $table->enum('ownership', ['owned', 'lease', 'rental',]);
            $table->string('manufacturer')->nullable();
            $table->string('base_machine')->nullable();
            $table->string('standard')->nullable();
            $table->date('introduction_date')->nullable();
            $table->date('return_date')->nullable();
            $table->decimal('current_hour_meter', 8, 1)->nullable();
            $table->string('manager_name')->nullable();
            $table->string('identification_number')->nullable()->unique();
            $table->date('next_maintenance_date')->nullable();
            $table->string('service_factory')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedInteger('order_no')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('machinery_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_category_id')->nullable()->constrained('machinery_categories')->nullOnDelete();
            $table->string('model_name');
            $table->string('manufacturer')->nullable();
            $table->timestamps();
        });
        Schema::create('machinery_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_master_id')->constrained('machinery_masters')->onDelete('cascade');
            $table->text('maintenance_details');
            $table->unsignedBigInteger('cost')->nullable();
            $table->date('effective_date')->index();
            $table->text('remarks')->nullable();
            $table->foreignId('input_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('input_date');
            $table->timestamps();
        });
        Schema::create('machinery_refuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_master_id')->constrained('machinery_masters')->onDelete('cascade');
            $table->date('refuel_date')->index();
            $table->decimal('fuel_volume', 6, 2);
            $table->string('gas_station')->nullable();
            $table->string('fuel_location')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('input_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('input_date')->nullable();
            $table->timestamps();
        });
        Schema::create('machinery_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_master_id')->constrained('machinery_masters')->onDelete('cascade');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('work_place')->nullable();
            $table->string('instruction_number')->nullable();
            $table->string('worker_group_in_use')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('input_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('input_date')->nullable();
            $table->decimal('hour_meter_start', 8, 1)->nullable();
            $table->decimal('hour_meter_end', 8, 1)->nullable();
            $table->timestamps();
        });
        Schema::create('machinery_master_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_master_id')->constrained('machinery_masters')->onDelete('cascade');
            $table->foreignId('machinery_attachment_id')->constrained('machinery_attachments')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(
                ['machinery_master_id', 'machinery_attachment_id'],
                'mma_master_attachment_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machinery_master_attachments');
        Schema::dropIfExists('machinery_usages');
        Schema::dropIfExists('machinery_refuels');
        Schema::dropIfExists('machinery_maintenances');
        Schema::dropIfExists('machinery_attachments');
        Schema::dropIfExists('machinery_masters');
        Schema::dropIfExists('machinery_categories');
    }
};
