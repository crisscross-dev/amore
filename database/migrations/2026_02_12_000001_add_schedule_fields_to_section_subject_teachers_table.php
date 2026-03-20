<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('section_subject_teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('section_subject_teachers', 'day_of_week')) {
                $table->string('day_of_week', 20)->nullable()->after('room');
            }
            if (!Schema::hasColumn('section_subject_teachers', 'start_time')) {
                $table->time('start_time')->nullable()->after('day_of_week');
            }
            if (!Schema::hasColumn('section_subject_teachers', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('section_subject_teachers', function (Blueprint $table) {
            if (Schema::hasColumn('section_subject_teachers', 'end_time')) {
                $table->dropColumn('end_time');
            }
            if (Schema::hasColumn('section_subject_teachers', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('section_subject_teachers', 'day_of_week')) {
                $table->dropColumn('day_of_week');
            }
        });
    }
};
