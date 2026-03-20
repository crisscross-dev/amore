<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subject_grade_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('grade_level', 20);
            $table->timestamps();

            $table->unique(['subject_id', 'grade_level']);
        });

        $subjects = DB::table('subjects')->select('id', 'grade_level')->get();
        foreach ($subjects as $subject) {
            $levels = $this->normalizeGradeLevels($subject->grade_level);
            foreach ($levels as $level) {
                DB::table('subject_grade_levels')->insert([
                    'subject_id' => $subject->id,
                    'grade_level' => $level,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_grade_levels');
    }

    private function normalizeGradeLevels(?string $gradeLevel): array
    {
        if (!$gradeLevel) {
            return [];
        }

        $normalized = strtolower(trim($gradeLevel));

        if ($normalized === 'all' || $normalized === 'all levels') {
            return ['7', '8', '9', '10', '11', '12'];
        }

        if (preg_match('/(7|8|9|10|11|12)/', $normalized, $matches)) {
            return [$matches[1]];
        }

        return [];
    }
};
