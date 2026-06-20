<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            $table->string('commission_number')->nullable();

            $table->string('commission_category')->default('داخلية');

            $table->foreignId('recipient_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();

            $table->string('calculation_type')->default('نسبة');
            $table->string('base_type')->default('قيمة العقد');

            $table->decimal('percentage', 8, 2)->nullable();
            $table->decimal('fixed_amount', 15, 2)->nullable();
            $table->decimal('calculated_amount', 15, 2)->default(0);

            $table->string('currency', 10)->default('SAR');

            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();

            $table->string('status')->default('مستحقة');

            $table->string('proof_file_path')->nullable();
            $table->string('drive_link')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_id');
            $table->index('commission_number');
            $table->index('commission_category');
            $table->index('recipient_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};