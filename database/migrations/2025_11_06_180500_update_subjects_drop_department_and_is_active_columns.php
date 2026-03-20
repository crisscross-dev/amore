<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'department')) {
                try {
                    $table->dropIndex('subjects_department_index');
                } catch (\Throwable $exception) {
                    // Index might not exist (fresh installs / sqlite); ignore.
                }
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('subjects', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'department')) {
                $table->string('department')->nullable()->after('grade_level');
            }

            if (! Schema::hasColumn('subjects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('hours_per_week');
            }
        });
    }
};
