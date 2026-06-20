<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('facility_name')->nullable();

            $table->string('commercial_registration_number')->nullable();
            $table->string('tax_number')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();

            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->text('address')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('facility_name');
            $table->index('commercial_registration_number');
            $table->index('tax_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};