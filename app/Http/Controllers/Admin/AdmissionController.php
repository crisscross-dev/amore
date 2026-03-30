<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');
        $query = $this->buildAdmissionsQuery($request, 'approved', true);

        $admissions = $query->latest()->paginate(15);
        $admissionsLiveSignature = $this->buildAdmissionsLiveSignature(
            $this->buildAdmissionsQuery($request, 'approved', false),
            'approved'
        );

        return view('admin.admissions.approved', compact(
            'admissions',
            'type',
            'search',
            'admissionsLiveSignature'
        ));
    }

    /**
     * Display a listing of all admissions
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $query = $this->buildAdmissionsQuery($request, 'pending', true);

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

        $admissionsLiveSignature = $this->buildAdmissionsLiveSignature(
            $this->buildAdmissionsQuery($request, 'pending', false),
            'pending'
        );

        return view('admin.admissions.index', compact(
            'admissions',
            'stats',
            'type',
            'status',
            'search',
            'admissionsLiveSignature'
        ));
    }

    /**
     * Lightweight polling endpoint for admissions queue updates.
     */
    public function liveSignature(Request $request)
    {
        $mode = $request->string('mode')->lower()->value() === 'approved' ? 'approved' : 'pending';
        $query = $this->buildAdmissionsQuery($request, $mode, false);

        return response()->json([
            'signature' => $this->buildAdmissionsLiveSignature($query, $mode),
            'generated_at' => now()->toIso8601String(),
        ]);
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

            $existingUser = User::where('email', $admission->email)->first();
            if ($existingUser) {
                DB::rollBack();
                return back()->with('error', 'A user account with this email already exists.');
            }

            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $generatedPassword = '';
            for ($i = 0; $i < 8; $i++) {
                $generatedPassword .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $studentUser = User::create([
                'account_type' => 'student',
                'first_name' => $admission->first_name,
                'middle_name' => $admission->middle_name,
                'last_name' => $admission->last_name,
                'email' => $admission->email,
                'contact_number' => $admission->phone,
                'password' => Hash::make($generatedPassword),
                'grade_level' => $admission->grade_level,
                'current_grade_level' => $admission->grade_level,
                'lrn' => $admission->lrn,
                'status' => 'active',
                'profile_picture' => 'default.jpg',
                'first_login' => true,
            ]);

            $admission->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
                'rejection_reason' => null,
                'user_id' => $studentUser->id,
                'temp_password' => $generatedPassword,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.admissions.index')
                ->with('success', 'Admission approved successfully! Student account was created and is ready for section assignment in Enrollment.');
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
                        $generatedPassword = null;
                        $createdUserId = $admission->user_id;

                        if (! $admission->user_id) {
                            $existingUser = $admission->email ? User::where('email', $admission->email)->first() : null;

                            if ($existingUser) {
                                $createdUserId = $existingUser->id;
                            } else {
                                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                                $generatedPassword = '';
                                for ($i = 0; $i < 8; $i++) {
                                    $generatedPassword .= $characters[random_int(0, strlen($characters) - 1)];
                                }

                                $studentUser = User::create([
                                    'account_type' => 'student',
                                    'first_name' => $admission->first_name,
                                    'middle_name' => $admission->middle_name,
                                    'last_name' => $admission->last_name,
                                    'email' => $admission->email,
                                    'contact_number' => $admission->phone,
                                    'password' => Hash::make($generatedPassword),
                                    'grade_level' => $admission->grade_level,
                                    'current_grade_level' => $admission->grade_level,
                                    'lrn' => $admission->lrn,
                                    'status' => 'active',
                                    'profile_picture' => 'default.jpg',
                                    'first_login' => true,
                                ]);

                                $createdUserId = $studentUser->id;
                            }
                        }

                        $admission->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'user_id' => $createdUserId,
                            'temp_password' => $generatedPassword,
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

    private function buildAdmissionsQuery(Request $request, string $mode, bool $withRelations): Builder
    {
        $type = (string) $request->get('type', 'all');
        $search = (string) $request->get('search', '');
        $status = (string) $request->get('status', 'all');

        $query = Admission::query();

        if ($withRelations) {
            $query->with(['user', 'approvedBy']);
        }

        if ($mode === 'approved') {
            $query->where('status', 'approved');
        } else {
            $statuses = ($status === 'all') ? ['pending', 'rejected'] : [$status];
            $query->whereIn('status', $statuses);
        }

        if ($type === 'jhs') {
            $query->where('school_level', 'jhs');
        } elseif ($type === 'shs') {
            $query->where('school_level', 'shs');
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('lrn', 'like', "%{$search}%")
                    ->orWhere('applicant_id', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function buildAdmissionsLiveSignature(Builder $filteredQuery, string $mode): string
    {
        $filteredCount = (clone $filteredQuery)->count();
        $filteredUpdatedStamp = $this->timestampOrZero((clone $filteredQuery)->max('updated_at'));
        $pendingCount = Admission::query()->where('status', 'pending')->count();
        $approvedCount = Admission::query()->where('status', 'approved')->count();
        $rejectedCount = Admission::query()->where('status', 'rejected')->count();
        $globalUpdatedStamp = $this->timestampOrZero(Admission::query()->max('updated_at'));

        return implode('|', [
            $mode,
            $filteredCount,
            $filteredUpdatedStamp,
            $pendingCount,
            $approvedCount,
            $rejectedCount,
            $globalUpdatedStamp,
        ]);
    }

    private function timestampOrZero($value): int
    {
        if (empty($value)) {
            return 0;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? $timestamp : 0;
    }
}
