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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'faculty_position_id')) {
                $table->foreignId('faculty_position_id')->nullable()->after('account_type')->constrained('faculty_positions')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('faculty_position_id');
            }

            if (!Schema::hasColumn('users', 'position_assigned_date')) {
                $table->date('position_assigned_date')->nullable()->after('department');
            }

            if (!Schema::hasColumn('users', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->after('position_assigned_date')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'faculty_position_id')) {
                $table->dropForeign(['faculty_position_id']);
            }

            if (Schema::hasColumn('users', 'assigned_by')) {
                $table->dropForeign(['assigned_by']);
            }

            $columns = collect([
                'faculty_position_id',
                'department',
                'position_assigned_date',
                'assigned_by',
            ])->filter(fn ($column) => Schema::hasColumn('users', $column))->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
