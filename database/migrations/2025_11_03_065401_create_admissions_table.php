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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            
            // Applicant ID and User Reference
            $table->string('applicant_id')->unique()->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // School Level
            $table->enum('school_level', ['jhs', 'shs'])->default('jhs');
            
            // Personal Information
            $table->string('lrn', 12);
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('dob');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('citizenship')->nullable();
            $table->string('religion')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('address', 500);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            
            // Academic Information
            $table->enum('school_type', ['Public', 'Private']);
            $table->enum('private_type', ['ESC', 'Non-ESC'])->nullable();
            $table->string('student_esc_no', 8)->nullable();
            $table->string('esc_school_id', 6)->nullable();
            $table->string('school_name');
            
            // SHS Specific - Strand Selection
            $table->enum('strand', ['STEM', 'ABM', 'HUMSS', 'TVL'])->nullable();
            $table->string('tvl_specialization')->nullable();
            
            // Family Information
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            
            // Status and Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('lrn');
            $table->index('school_level');
            $table->index('status');
            $table->index('applicant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
