<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            $table->foreignId('contract_id')
                ->nullable()
                ->constrained('contracts')
                ->nullOnDelete();

            $table->string('payment_number')->nullable();

            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('SAR');

            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();

            $table->string('payment_method')->nullable();

            $table->string('status')->default('مستحقة');

            $table->string('proof_file_path')->nullable();
            $table->string('drive_link')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_id');
            $table->index('contract_id');
            $table->index('payment_number');
            $table->index('status');
            $table->index('due_date');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};