<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_type_document_requirements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_type_id');

            $table->string('name');
            $table->text('description')->nullable();

            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('transaction_type_id', 'tt_doc_req_type_fk')
                ->references('id')
                ->on('transaction_types')
                ->cascadeOnDelete();

            $table->index('transaction_type_id', 'tt_doc_req_type_idx');
            $table->index('is_required', 'tt_doc_req_required_idx');
            $table->index('is_active', 'tt_doc_req_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_type_document_requirements');
    }
};