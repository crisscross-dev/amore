<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_grade_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_label', 255)->nullable();
            $table->string('term', 20)->nullable();
            $table->unsignedInteger('edited_entries_count')->default(0);
            $table->unsignedInteger('edited_students_count')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index('edited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_grade_edit_logs');
    }
};
