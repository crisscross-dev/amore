<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'adviser_id')) {
                $table->foreignId('adviser_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'adviser_id')) {
                $table->dropForeign(['adviser_id']);
                $table->dropColumn('adviser_id');
            }
        });
    }
};
