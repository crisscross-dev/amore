<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AdmissionController extends Controller
{
    /**
     * Display a listing of approved admissions only
     */
    public function approved(Request $request)
    {
        $type = $request->get('type', 'all'); // all, jhs, shs
        $search = $request->get('search', '');

        // Fetch only approved admissions
        $query = Admission::with(['user', 'approvedBy'])->where('status', 'approved');

        // Apply type filter
        if ($type === 'jhs') {
            $query->where('school_level', 'jhs');
        } elseif ($type === 'shs') {
            $query->where('school_level', 'shs');
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhere('applicant_id', 'like', "%{$search}%");
            });
        }

        $admissions = $query->latest()->paginate(15);

        return view('admin.admissions.approved', compact('admissions', 'type', 'search'));
    }
    
    /**
     * Display a listing of all admissions
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, jhs, shs
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');

        // Only show pending and rejected admissions unless status filter is set
        $statuses = ($status === 'all') ? ['pending', 'rejected'] : [$status];

        // Base query
        $query = Admission::with(['user', 'approvedBy'])->whereIn('status', $statuses);
        
        // Apply type filter
        if ($type === 'jhs') {
            $query->where('school_level', 'jhs');
        } elseif ($type === 'shs') {
            $query->where('school_level', 'shs');
        }
        
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhere('applicant_id', 'like', "%{$search}%");
            });
        }
        
        $admissions = $query->latest()->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => Admission::count(),
            'pending' => Admission::pending()->count(),
            'approved' => Admission::approved()->count(),
            'rejected' => Admission::rejected()->count(),
            'jhs_total' => Admission::jhs()->count(),
            'shs_total' => Admission::shs()->count(),
        ];
        
        return view('admin.admissions.index', compact('admissions', 'stats', 'type', 'status', 'search'));
    }

    /**
     * Display the specified admission
     */
    public function show(Request $request, $type, $id)
    {
        $admission = Admission::with(['user', 'approvedBy'])->findOrFail($id);
        
        // Verify the admission type matches
        if ($admission->school_level !== $type) {
            abort(404);
        }
        
        return view('admin.admissions.show', compact('admission'));
    }

    /**
     * Approve an admission
     */
    public function approve(Request $request, $type, $id)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            $admission = Admission::findOrFail($id);
            
            // Verify the admission type matches
            if ($admission->school_level !== $type) {
                abort(404);
            }
            
            // Check if user account already exists
            $existingUser = User::where('email', $admission->email)->first();
            
            if ($existingUser) {
                DB::rollBack();
                return back()->with('error', 'A user account with this email already exists.');
            }
            
            // Generate random alphanumeric password (8 characters: letters and numbers)
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $generatedPassword = '';
            for ($i = 0; $i < 8; $i++) {
                $generatedPassword .= $characters[random_int(0, strlen($characters) - 1)];
            }
            
            // Use the grade level from admission form
            $gradeLevel = $admission->grade_level;
            
            // For SHS, append strand to grade level if needed
            if (strtoupper($admission->school_level) === 'SHS' && $admission->strand && !str_contains($gradeLevel, $admission->strand)) {
                $gradeLevel = $gradeLevel . ' - ' . $admission->strand;
            }
            
            // Generate custom student ID
            $lastStudent = User::where('account_type', 'student')
                ->where('custom_id', 'like', 'STD-%')
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastStudent && $lastStudent->custom_id) {
                $lastNumber = (int) substr($lastStudent->custom_id, 4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $customId = 'STD-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            // Create user account
            $user = User::create([
                'account_type' => 'student',
                'first_name' => $admission->first_name,
                'middle_name' => $admission->middle_name,
                'last_name' => $admission->last_name,
                'email' => $admission->email,
                'contact_number' => $admission->phone,
                'password' => Hash::make($generatedPassword),
                'custom_id' => $customId,
                'grade_level' => $gradeLevel,
                'current_grade_level' => $admission->grade_level, // For enrollment system
                'lrn' => $admission->lrn,
                'status' => 'active', // Set account as active
                'profile_picture' => 'default.jpg',
                'first_login' => true,
            ]);
            
            // Update admission with user_id and approval details
            $admission->update([
                'user_id' => $user->id,
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
                'rejection_reason' => null,
                'temp_password' => $generatedPassword, // Store plain password temporarily
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.admissions.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Admission approved successfully! Student account created with password: ' . $generatedPassword);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve admission: ' . $e->getMessage());
        }
    }

    /**
     * Reject an admission
     */
    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            $admission = Admission::findOrFail($id);
            
            // Verify the admission type matches
            if ($admission->school_level !== $type) {
                abort(404);
            }
            
            $admission->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'approval_notes' => null,
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.admissions.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Admission rejected successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject admission: ' . $e->getMessage());
        }
    }

    /**
     * Delete/Remove an admission application
     */
    public function destroy($type, $id)
    {
        DB::beginTransaction();
        try {
            $admission = Admission::findOrFail($id);
            
            // Verify the admission type matches
            if ($admission->school_level !== $type) {
                abort(404);
            }
            
            // Store the name for the success message
            $fullName = $admission->full_name;
            
            // Delete the admission
            $admission->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.admissions.index')
                ->with('success', "Application for {$fullName} has been permanently removed.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove admission: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admissions' => 'required|array',
            'admissions.*.type' => 'required|in:jhs,shs',
            'admissions.*.id' => 'required|integer',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            $successCount = 0;
            
            foreach ($request->admissions as $item) {
                $admission = Admission::find($item['id']);
                
                if ($admission && $admission->school_level === $item['type']) {
                    if ($request->action === 'approve') {
                        $admission->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);
                    } else {
                        $admission->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'rejection_reason' => $request->rejection_reason,
                        ]);
                    }
                    $successCount++;
                }
            }
            
            DB::commit();
            
            $actionText = $request->action === 'approve' ? 'approved' : 'rejected';
            return back()->with('success', "{$successCount} admission(s) {$actionText} successfully!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }
}
