<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // if linked to logged in user
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birthdate');
            $table->string('gender');
            $table->string('place_of_birth');
            $table->string('nationality');
            $table->string('religion');
            $table->string('contact');
            $table->string('email');
            $table->string('house_number');
            $table->string('street');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->string('enrollment_type');
            $table->date('enrollment_date');
            $table->string('grade_level');
            $table->string('track_strand')->nullable();
            $table->string('lrn');
            $table->string('previous_school');
            $table->string('school_address');
            $table->decimal('general_average', 5, 2);
            $table->string('last_grade_completed');
            $table->string('school_doc')->nullable();
            $table->string('father_name');
            $table->string('father_contact');
            $table->string('father_occupation');
            $table->string('mother_name');
            $table->string('mother_contact');
            $table->string('mother_occupation');
            $table->string('guardian_name');
            $table->string('relationship');
            $table->string('parent_contact');
            $table->string('parent_address');
            $table->string('emergency_name');
            $table->string('emergency_relationship');
            $table->string('emergency_contact');
            $table->string('profile_pic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
