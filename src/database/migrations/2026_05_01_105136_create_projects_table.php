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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->nullable()->comment('指示書番号');
            $table->string('site_manager')->nullable()->comment('現場作業責任者');
            $table->date('ordered_on')->nullable()->comment('指示日');
            $table->string('staff_name')->nullable()->comment('担当者');
            $table->string('site_alias')->comment('現場通称');
            $table->string('owner_name')->comment('所有者名');
            $table->string('owner_contact')->nullable()->comment('所有者住所');
            $table->string('location_address')->nullable()->comment('作業番所-住所');
            $table->string('location_subcompartment')->nullable()->comment('作業番所-林小班');
            $table->string('project_name')->nullable()->comment('事業名');
            $table->text('work_content')->nullable()->comment('作業内容');
            $table->string('work_volume')->nullable()->comment('作業量');
            $table->date('started_on')->nullable()->comment('着工日');
            $table->date('completed_on')->nullable()->comment('完了日');
            $table->string('machines')->nullable()->comment('使用機械');
            $table->string('work_cost')->nullable()->comment('作業費');
            $table->text('notes')->nullable()->comment('作業留意点');
            $table->string('attachments')->nullable()->comment('添付書類');
            $table->decimal('area_value', 10)->nullable()->comment('面積');
            $table->string('area_unit', 10)->nullable()->comment('単位');
            $table->decimal('volume_value', 10)->nullable()->comment('材積');
            $table->string('volume_unit', 10)->nullable()->comment('単位');
            $table->decimal('labor_units', 10)->nullable()->comment('人区');
            $table->string('team_name')->nullable()->comment('施行班');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->integer('fiscal_year')->comment('年度');
            $table->text('note')->nullable()->comment('備考');
            $table->timestamps();
            $table->string('order_type')->default('一般')->comment('指示書種類');
            $table->string('business_type')->nullable()->comment('業務種類');
            $table->enum('is_protection_forest', ['有', '無'])->nullable()->comment('保安林の有無');
            $table->date('logging_notice_date')->nullable()->comment('伐採届提出日');
            $table->date('subsidy_grant_date')->nullable()->comment('補助金交付年月日');
            $table->string('operation_history')->nullable()->comment('施業履歴');
            $table->string('first_approver')->nullable()->comment('一次承認者');
            $table->string('second_approver')->nullable()->comment('二次承認者');
            $table->string('workloaded_title1')->nullable()->comment('作業量1');
            $table->string('area_or_volume1')->nullable()->comment('面積/作業量1');
            $table->string('workloaded_title2')->nullable()->comment('作業量2');
            $table->string('area_or_volume2')->nullable()->comment('面積/作業量2');
            $table->string('workloaded_title3')->nullable()->comment('作業量3');
            $table->string('area_or_volume3')->nullable()->comment('面積/作業量3');
            $table->string('workloaded_title4')->nullable()->comment('作業量4');
            $table->string('area_or_volume4')->nullable()->comment('面積/作業量4');

            $table->string('unit_price')->nullable()->comment('作業単価');
            $table->bigInteger('expected_yield1')->nullable()->comment('出材予定量1');
            $table->bigInteger('expected_yield2')->nullable()->comment('出材予定量2');
            $table->bigInteger('personnel')->nullable()->comment('作業人数');
            $table->bigInteger('average_sales')->nullable()->comment('平均売上額');
            $table->foreign('group_id')
                ->references('id')
                ->on('workergroups_v2')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
