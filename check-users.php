<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CURRENT USERS IN DATABASE ===\n\n";
echo str_repeat("=", 100) . "\n";
printf("%-5s | %-20s | %-30s | %-10s | %-15s | %-15s\n", 
    "ID", "Name", "Email", "Type", "Grade", "Custom ID");
echo str_repeat("=", 100) . "\n";

$users = DB::table('users')->get();

if ($users->isEmpty()) {
    echo "No users found in database.\n\n";
} else {
    foreach ($users as $user) {
        printf("%-5s | %-20s | %-30s | %-10s | %-15s | %-15s\n",
            $user->id,
            substr($user->first_name . ' ' . $user->last_name, 0, 20),
            substr($user->email, 0, 30),
            $user->account_type ?? 'N/A',
            $user->grade_level ?? 'N/A',
            $user->custom_id ?? 'N/A'
        );
    }
}

echo str_repeat("=", 100) . "\n\n";

// Summary
$admin_count = DB::table('users')->where('account_type', 'admin')->count();
$faculty_count = DB::table('users')->where('account_type', 'faculty')->count();
$student_count = DB::table('users')->where('account_type', 'student')->count();

echo "SUMMARY:\n";
echo "--------\n";
echo "Admin:   $admin_count\n";
echo "Faculty: $faculty_count\n";
echo "Student: $student_count\n";
echo "Total:   " . ($admin_count + $faculty_count + $student_count) . "\n\n";

// Check for specific grade levels
if ($student_count > 0) {
    $jhs_count = DB::table('users')
        ->where('account_type', 'student')
        ->where('grade_level', 'LIKE', '%Grade%7%')
        ->orWhere('grade_level', 'LIKE', '%Grade%8%')
        ->orWhere('grade_level', 'LIKE', '%Grade%9%')
        ->orWhere('grade_level', 'LIKE', '%Grade%10%')
        ->count();
    
    $shs_count = DB::table('users')
        ->where('account_type', 'student')
        ->where('grade_level', 'LIKE', '%Grade%11%')
        ->orWhere('grade_level', 'LIKE', '%Grade%12%')
        ->count();
    
    echo "\nStudent Breakdown:\n";
    echo "JHS (Grades 7-10): ~$jhs_count\n";
    echo "SHS (Grades 11-12): ~$shs_count\n";
}

echo "\n";
