<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('document_requirement_id')->nullable();

            $table->string('name');
            $table->string('status')->default('ناقص');

            $table->string('file_path')->nullable();
            $table->string('drive_link')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('transaction_id', 'trx_docs_trx_fk')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete();

            $table->foreign('document_requirement_id', 'trx_docs_req_fk')
                ->references('id')
                ->on('transaction_type_document_requirements')
                ->nullOnDelete();

            $table->index('transaction_id', 'trx_docs_trx_idx');
            $table->index('document_requirement_id', 'trx_docs_req_idx');
            $table->index('status', 'trx_docs_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_documents');
    }
};