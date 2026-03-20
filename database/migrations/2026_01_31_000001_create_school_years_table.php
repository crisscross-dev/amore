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
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('year_name'); // e.g., "2026-2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->date('enrollment_start'); // When enrollment opens
            $table->date('enrollment_end');   // When enrollment closes
            $table->boolean('is_active')->default(false); // Only one active at a time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
