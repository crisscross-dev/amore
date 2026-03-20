<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentEnrollmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'document_type',
        'document_name',
        'file_path',
    ];

    /**
     * Get the enrollment this document belongs to
     */
    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    /**
     * Get the full URL of the document
     */
    public function getFileUrl()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSize()
    {
        if (Storage::exists($this->file_path)) {
            $bytes = Storage::size($this->file_path);
            return $this->formatBytes($bytes);
        }
        return 'Unknown';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
