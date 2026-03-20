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
        if (Schema::hasTable('subjects')) {
            return;
        }

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('subject_type', ['core', 'elective', 'specialized', 'extracurricular'])->nullable()->default('core');
            $table->enum('grade_level', ['7', '8', '9', '10', '11', '12', 'all'])->default('all');
            $table->string('department')->nullable();
            $table->integer('hours_per_week')->nullable()->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['subject_type', 'grade_level']);
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
