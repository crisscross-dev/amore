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
        // Add approval fields to JHS admissions
        Schema::table('jhs_admissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'waitlisted'])->default('pending')->after('application_status');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approval_notes');
            
            // Add index for status
            $table->index('status');
        });

        // Add approval fields to SHS admissions
        Schema::table('shs_admissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'waitlisted'])->default('pending')->after('application_status');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approval_notes');
            
            // Add index for status
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove approval fields from JHS admissions
        Schema::table('jhs_admissions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'approval_notes', 'rejection_reason']);
        });

        // Remove approval fields from SHS admissions
        Schema::table('shs_admissions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'approval_notes', 'rejection_reason']);
        });
    }
};
