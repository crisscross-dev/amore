<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grade_entries', function (Blueprint $table) {
            $table->text('faculty_remark')->nullable()->after('grade_value');
        });
    }

    public function down(): void
    {
        Schema::table('grade_entries', function (Blueprint $table) {
            $table->dropColumn('faculty_remark');
        });
    }
};
