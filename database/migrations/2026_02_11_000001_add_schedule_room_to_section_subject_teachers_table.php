<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('section_subject_teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('section_subject_teachers', 'schedule')) {
                $table->string('schedule')->nullable()->after('teacher_id');
            }
            if (!Schema::hasColumn('section_subject_teachers', 'room')) {
                $table->string('room')->nullable()->after('schedule');
            }
        });
    }

    public function down(): void
    {
        Schema::table('section_subject_teachers', function (Blueprint $table) {
            if (Schema::hasColumn('section_subject_teachers', 'room')) {
                $table->dropColumn('room');
            }
            if (Schema::hasColumn('section_subject_teachers', 'schedule')) {
                $table->dropColumn('schedule');
            }
        });
    }
};
