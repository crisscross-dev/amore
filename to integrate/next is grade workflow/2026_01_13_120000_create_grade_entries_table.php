<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('term', 20); // e.g., Q1, Q2, Midterm
            $table->decimal('grade_value', 5, 2); // 0-100 or 0-4.00 scale
            $table->string('status', 20)->default('draft'); // draft|submitted|approved|rejected
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'subject_id', 'term']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
    }
};
