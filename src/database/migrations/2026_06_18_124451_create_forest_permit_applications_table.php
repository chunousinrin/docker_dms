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
        Schema::create('forest_permit_applications', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('management_no')->comment('整理番号');
            $table->unsignedSmallInteger('branch_no')->default(0)->comment('枝番');

            $table->string('facility_name')->nullable()->comment('施設名称');
            $table->string('protection_forest_type', 50)->nullable()->comment('保安林種');
            $table->string('forest_location')->nullable()->comment('森林所在地');

            $table->decimal('parcel_count', 8, 2)->nullable()->comment('筆数');

            $table->string('applicant_name')->nullable()->comment('申請者');
            $table->string('contact')->nullable()->comment('連絡先');

            $table->decimal('permit_area', 15, 5)->nullable()->comment('許可面積');

            $table->date('application_date')->comment('申請年月日');
            $table->date('permit_date')->comment('許可年月日');

            $table->string('order_no', 50)->nullable()->comment('指令番号');

            $table->date('permit_start_date')->comment('許可始期');
            $table->date('permit_end_date')->comment('許可終期');

            $table->date('clear_cut_completion_date')->nullable()->comment('皆伐完了日');
            $table->date('planting_completion_date')->nullable()->comment('植栽完了日');

            $table->date('submission_date')->nullable()->comment('提出日');
            $table->date('start_date')->nullable()->comment('着手日');
            $table->date('completion_date')->nullable()->comment('完了日');

            $table->text('remarks')->nullable()->comment('備考');

            $table->timestamps();

            $table->unique([
                'management_no',
                'branch_no'
            ]);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forest_permit_applications');
    }
};
