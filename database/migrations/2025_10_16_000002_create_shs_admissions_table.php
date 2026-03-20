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
        Schema::create('shs_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id')->unique();
            $table->string('lrn', 12);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');
            $table->integer('age');
            $table->string('gender');
            $table->string('citizenship');
            $table->string('religion');
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->text('address');
            
            // Academic Information
            $table->string('applying_for_grade');
            $table->string('school_year');
            $table->string('strand');
            $table->string('tvl_specialization')->nullable();
            $table->string('previous_school');
            $table->string('school_type');
            $table->string('private_school_type')->nullable();
            $table->string('esc_student_no')->nullable();
            $table->string('esc_school_id')->nullable();
            
            // Parent Information
            $table->string('father_name');
            $table->string('father_occupation');
            $table->string('mother_maiden_name');
            $table->string('mother_occupation');
            
            // Application Status
            $table->string('application_status')->default('pending');
            $table->dateTime('application_date');
            $table->boolean('confirm_details')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('application_status');
            $table->index('school_year');
            $table->index('strand');
            $table->index('lrn');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shs_admissions');
    }
};
