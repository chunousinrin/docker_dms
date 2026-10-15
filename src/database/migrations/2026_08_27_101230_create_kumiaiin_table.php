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

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('地区名');
            $table->unsignedInteger('sort_order')->default(0)->comment('表示順');
            $table->string('code', 20)->nullable()->unique()->comment('地区コード（任意）');
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete()->comment('所属地区');
            $table->string('name', 100)->comment('字名');
            $table->string('remarks', 20)->nullable()->comment('摘要');
            $table->string('code', 20)->nullable()->comment('字コード（任意）');
            $table->unsignedInteger('sort_order')->default(0)->comment('表示順');
            $table->timestamps();

            $table->unique(['district_id', 'name']);
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // 基本情報
            $table->string('member_number', 20)->unique()->comment('組合員番号');
            $table->string('furigana', 100)->nullable()->comment('フリガナ');
            $table->string('name1', 100)->nullable()->comment('氏名1');
            $table->string('name2', 100)->nullable()->comment('氏名2（連名・代表者など）');
            $table->string('postal_code', 8)->nullable()->comment('郵便番号');
            $table->string('address1', 255)->nullable()->comment('住所1');
            $table->string('address2', 255)->nullable()->comment('住所2');
            $table->string('occupation', 100)->nullable()->comment('職業');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('性別');
            $table->date('birth_date')->nullable()->comment('生年月日');
            $table->string('phone_number', 20)->nullable()->comment('電話番号');

            // 組合員区分・税区分
            $table->string('member_type', 50)->nullable()->comment('組合員区分（正組合員/准組合員 等）');
            $table->string('withholding_tax_type', 50)->nullable()->comment('源泉税区分');

            // 加入・脱退
            $table->date('joined_date')->nullable()->comment('加入年月日');
            $table->date('withdrawn_date')->nullable()->comment('脱退年月日');

            // 所属
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete()->comment('所属地区');
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete()->comment('所属字');
            $table->string('selection', 100)->nullable()->comment('選択');

            // その他
            $table->text('remarks')->nullable()->comment('備考');
            $table->string('honorific', 10)->nullable()->default('様')->comment('敬称');
            $table->boolean('newsletter_delivery')->default(true)->comment('広報送付');
            $table->date('last_updated_date')->nullable()->comment('最終更新日（業務上の更新日。updated_atとは別管理）');
            $table->boolean('is_soudai')->default(false)->comment('総代');
            $table->string('election_district', 100)->nullable()->comment('選挙区');

            $table->timestamps();
            $table->softDeletes();

            $table->index('furigana');
            $table->index('withdrawn_date');
            // district_id / section_id は foreignId() が自動でインデックスを付与済み
        });

        Schema::create('capital_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            $table->date('transaction_date')->comment('異動年月日');
            $table->enum('transaction_type', ['increase', 'decrease'])->comment('増口/減口');
            $table->unsignedInteger('units')->comment('口数');
            $table->unsignedBigInteger('amount')->comment('金額');
            $table->unsignedBigInteger('balance_units_after')->nullable()->comment('異動後口数残高');
            $table->unsignedBigInteger('balance_amount_after')->nullable()->comment('異動後金額残高');
            $table->string('reason', 100)->nullable()->comment('異動事由（増資/払込/譲渡/脱退精算 等）');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['member_id', 'transaction_date']);
        });

        Schema::create('forest_area_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            $table->date('transaction_date')->comment('異動年月日');
            $table->enum('transaction_type', ['increase', 'decrease'])->comment('増加/減少');
            $table->decimal('area_ha', 10, 4)->comment('異動面積（ha）');
            $table->decimal('balance_area_ha_after', 10, 4)->nullable()->comment('異動後所有面積残高（ha）');
            $table->string('location', 255)->nullable()->comment('林班・字など所在地');
            $table->string('reason', 100)->nullable()->comment('異動事由（取得/売買/相続/伐採 等）');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['member_id', 'transaction_date']);
        });

        Schema::create('deposit_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('issue_number')->comment('その組合員における発行回数（1,2,3...）');
            $table->date('issue_date')->comment('発行年月日');
            $table->unsignedBigInteger('capital_amount_at_issue')->nullable()->comment('発行時点の出資金額');
            $table->string('reason', 100)->nullable()->comment('発行事由（新規/再発行/紛失 等）');
            $table->string('issued_by', 100)->nullable()->comment('発行担当者');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(['member_id', 'issue_number']);
        });

        Schema::create('newsletter_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_name', 100)->comment('号数・タイトル（例：広報〇〇年〇月号）');
            $table->date('published_date')->nullable()->comment('発行年月日');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('newsletter_issue_id')->nullable()->constrained('newsletter_issues')->nullOnDelete();

            $table->date('sent_date')->comment('発送年月日');
            $table->enum('method', ['mail', 'hand', 'other'])->default('mail')->comment('発送方法');
            $table->enum('status', ['sent', 'returned', 'skipped'])->default('sent')->comment('発送状況（送付済/宛先不明で返戻/送付対象外）');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['member_id', 'sent_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('newsletter_shipments');
        Schema::dropIfExists('newsletter_issues');

        Schema::dropIfExists('deposit_receipts');

        Schema::dropIfExists('forest_area_transactions');

        Schema::dropIfExists('capital_transactions');

        Schema::dropIfExists('members');

        Schema::dropIfExists('sections');

        Schema::dropIfExists('districts');
    }
};
