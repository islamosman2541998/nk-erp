<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            $table->string('expense_number')->nullable();

            $table->string('category')->nullable();
            $table->string('title');

            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('SAR');

            $table->date('expense_date')->nullable();

            $table->string('paid_to')->nullable();
            $table->string('payment_method')->nullable();

            $table->string('status')->default('مدفوع');

            $table->string('receipt_file_path')->nullable();
            $table->string('drive_link')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_id');
            $table->index('expense_number');
            $table->index('category');
            $table->index('status');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};