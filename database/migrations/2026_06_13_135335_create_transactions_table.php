<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Basic
            $table->string('reference_number')->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('transaction_type_id')->constrained('transaction_types')->restrictOnDelete();
            $table->foreignId('transaction_subtype_id')->nullable()->constrained('transaction_types')->nullOnDelete();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Status
            $table->string('status')->default('تحت الإجراء');
            $table->string('internal_status')->nullable();
            $table->string('priority')->nullable();

            // Project / Site Data
            $table->string('project_name')->nullable();
            $table->text('project_location')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();

            $table->string('activity_type')->nullable();
            $table->string('activity_code')->nullable();
            $table->string('category')->nullable();

            // Authority / Center Data
            $table->string('center_request_number')->nullable();
            $table->string('authority_name')->nullable();
            $table->string('authority_reference_number')->nullable();

            // Permit Data
            $table->string('permit_number')->nullable();
            $table->date('permit_issued_at')->nullable();
            $table->date('permit_expires_at')->nullable();
            $table->boolean('permit_needs_renewal')->default(false);

            // Assigned Users
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('technical_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('financial_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Dates
            $table->date('started_at')->nullable();
            $table->date('expected_delivery_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Links
            $table->string('main_drive_link')->nullable();
            $table->string('meetings_drive_link')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('reference_number');
            $table->index('client_id');
            $table->index('transaction_type_id');
            $table->index('status');
            $table->index('internal_status');
            $table->index('assigned_to');
            $table->index('technical_manager_id');
            $table->index('coordinator_id');
            $table->index('financial_user_id');
            $table->index('permit_number');
            $table->index('permit_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};