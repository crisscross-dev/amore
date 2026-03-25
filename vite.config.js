import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/admission.js',
                'resources/js/admin-calendar.js',
                'resources/js/announcements.js',
                'resources/js/admissions.js',
                'resources/js/register.js',
                'resources/js/admin-accounts.js',
                'resources/js/admin-sections.js',
                'resources/js/student-sections.js',
                'resources/css/auth.css',
                'resources/js/auth.js',
                'resources/css/layouts/dashboard-roles/dashboard-student.css',
                'resources/css/layouts/dashboard-roles/dashboard-faculty.css',
                'resources/css/layouts/dashboard-roles/dashboard-admin.css',
                'resources/css/admin/dashboard-admin-edit.css',
                'resources/css/faculty/dashboard-faculty-edit.css',
                'resources/css/student/dashboard-student-edit.css',
                'resources/css/admin/faculty-management.css',
                'resources/css/admin/subject-management.css',
                'resources/css/admin/grade-approvals.css',
                'resources/css/admin/grade-management.css',
                'resources/css/faculty/grade-management.css',
                'resources/css/student/grade-view.css',
                'resources/css/pagination.css',
                'resources/css/swal-custom.css',
                // New Admission System Assets
                'resources/css/admissions.css',
                'resources/js/admissions/jhs.js',
                'resources/js/admissions/shs.js',
                // Home/Welcome Page Assets
                'resources/css/home/news-faculty.css',
                'resources/js/home/news-faculty.js',
            ],
            refresh: true,
        }),
    ],
});
