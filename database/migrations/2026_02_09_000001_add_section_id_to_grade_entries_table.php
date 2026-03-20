<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grade_entries', function (Blueprint $table) {
            $table->foreignId('section_id')->after('student_id')->constrained('sections')->cascadeOnDelete();
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::table('grade_entries', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
