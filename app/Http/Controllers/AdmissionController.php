<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Admission;
use App\Models\JhsAdmission;
use App\Models\ShsAdmission;

class AdmissionController extends Controller
{
    /**
     * Show the admission selection page
     */
    public function selection()
    {
        return view('admissions.selection');
    }

    /**
     * Show the JHS admission form
     */
    public function jhsForm()
    {
        return view('admissions.jhs');
    }

    /**
     * Show the SHS admission form
     */
    public function shsForm()
    {
        return view('admissions.shs');
    }

    /**
     * Process JHS admission form submission
     */
    public function jhsStore(Request $request)
    {
        $rules = $this->getValidationRules($request);
        $rules['grade_level'] = 'required|in:Grade 7,Grade 8,Grade 9,Grade 10';
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process JHS admission
        $data = $request->all();
        $data['school_level'] = 'jhs';
        $data['status'] = 'pending';
        
        // Save to database
        $admission = Admission::create($data);

        // Redirect to requirements page with admission details
        return redirect()
            ->route('admissions.requirements', ['id' => $admission->id])
            ->with('admission', $admission);
    }

    /**
     * Process SHS admission form submission
     */
    public function shsStore(Request $request)
    {
        $rules = $this->getValidationRules($request);
        $rules['grade_level'] = 'required|in:Grade 11,Grade 12';
        
        // Add SHS-specific validation for strand
        $rules['strand'] = 'required|in:STEM,ABM,HUMSS,TVL';
        
        // Add TVL specialization validation if TVL is selected
        if ($request->strand === 'TVL') {
            $rules['tvl_specialization'] = 'required|string|max:255';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process SHS admission
        $data = $request->all();
        $data['school_level'] = 'shs';
        $data['status'] = 'pending';
        
        // Save to database
        $admission = Admission::create($data);

        // Redirect to requirements page with admission details
        return redirect()
            ->route('admissions.requirements', ['id' => $admission->id])
            ->with('admission', $admission);
    }

    /**
     * Show requirements page after submission
     */
    public function requirements($id)
    {
        $admission = Admission::findOrFail($id);
        return view('admissions.requirements', compact('admission'));
    }

    /**
     * Get validation rules based on school type
     */
    private function getValidationRules(Request $request)
    {
        $rules = [
            'lrn' => 'required|digits:12',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'age' => 'required|integer|min:1|max:100',
            'gender' => 'required|in:Male,Female',
            'citizenship' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'height' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'school_type' => 'required|in:Public,Private',
            'mother_name' => 'required|string|max:255',
            'mother_occupation' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
        ];

        // Add conditional validation based on school type
        if ($request->school_type === 'Public') {
            $rules['school_name'] = 'required|string|max:255';
        } elseif ($request->school_type === 'Private') {
            $rules['private_type'] = 'required|in:ESC,Non-ESC';
            
            if ($request->private_type === 'ESC') {
                $rules['student_esc_no'] = 'required|string|max:8';
                $rules['esc_school_id'] = 'required|string|max:6';
                $rules['school_name'] = 'required|string|max:255';
            } elseif ($request->private_type === 'Non-ESC') {
                $rules['school_name'] = 'required|string|max:255';
            }
        }

        return $rules;
    }

    public function showAdmissionForm()
    {
        return view('admission');
    }

    public function storeAdmission(Request $request)
    {
        $admissionType = $request->input('admission_type', 'jhs');

        $rules = [
            'admission_type' => 'required|in:jhs,shs',
            'applicant_id' => 'required|string|max:255',
            'lrn' => 'required|string|size:12',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
            'age' => 'required|integer|min:10|max:25',
            'gender' => 'required|in:Male,Female',
            'religion' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'applying_for_grade' => 'required|string',
            'school_year' => 'required|string',
            'previous_school' => 'required|string|max:255',
            'school_type' => 'required|in:Public,Private',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'required|string|max:255',
            'mother_maiden_name' => 'required|string|max:255',
            'mother_occupation' => 'required|string|max:255',
            'application_date' => 'required|date',
            'confirm_details' => 'required|accepted',
        ];

        if ($admissionType === 'shs') {
            $rules = array_merge($rules, [
                'citizenship' => 'required|string|max:255',
                'height' => 'nullable|numeric|min:100|max:250',
                'weight' => 'nullable|numeric|min:20|max:200',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'strand' => 'required|in:STEM,ABM,HUMSS,GAS,TVL',
                'tvl_specialization' => 'required_if:strand,TVL|nullable|string',
                'private_school_type' => 'nullable|in:Sectarian,Non-Sectarian',
                'esc_student_no' => 'nullable|string|max:255',
                'esc_school_id' => 'nullable|string|max:255',
            ]);
        }

        $validated = $request->validate($rules);
        $validated['application_status'] = 'pending';

        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        }

        try {
            if ($admissionType === 'jhs') {
                $admission = JhsAdmission::create($validated);
            } else {
                $admission = ShsAdmission::create($validated);
            }

            return redirect()
                ->route('admission')
                ->with('success', 'Your admission application has been submitted successfully! Application ID: ' . $admission->applicant_id);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit application. Please try again.']);
        }
    }

    public function searchAdmission(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide an Application ID or LRN'
            ], 400);
        }

        $jhsAdmission = JhsAdmission::where('applicant_id', $query)
            ->orWhere('lrn', $query)
            ->first();

        if ($jhsAdmission) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $jhsAdmission->id,
                    'applicant_id' => $jhsAdmission->applicant_id,
                    'full_name' => $jhsAdmission->full_name,
                    'lrn' => $jhsAdmission->lrn,
                    'admission_type' => 'JHS',
                    'admission_type_full' => 'Junior High School',
                    'applying_for_grade' => $jhsAdmission->applying_for_grade,
                    'status' => $jhsAdmission->status,
                    'application_date' => $jhsAdmission->application_date->format('F d, Y'),
                    'approved_at' => $jhsAdmission->approved_at ? $jhsAdmission->approved_at->format('F d, Y') : null,
                    'approval_notes' => $jhsAdmission->approval_notes,
                    'rejection_reason' => $jhsAdmission->rejection_reason,
                ]
            ]);
        }

        $shsAdmission = ShsAdmission::where('applicant_id', $query)
            ->orWhere('lrn', $query)
            ->first();

        if ($shsAdmission) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $shsAdmission->id,
                    'applicant_id' => $shsAdmission->applicant_id,
                    'full_name' => $shsAdmission->full_name,
                    'lrn' => $shsAdmission->lrn,
                    'admission_type' => 'SHS',
                    'admission_type_full' => 'Senior High School',
                    'applying_for_grade' => $shsAdmission->applying_for_grade,
                    'strand' => $shsAdmission->strand ?? 'N/A',
                    'status' => $shsAdmission->status,
                    'application_date' => $shsAdmission->application_date->format('F d, Y'),
                    'approved_at' => $shsAdmission->approved_at ? $shsAdmission->approved_at->format('F d, Y') : null,
                    'approval_notes' => $shsAdmission->approval_notes,
                    'rejection_reason' => $shsAdmission->rejection_reason,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No application found with the provided ID or LRN'
        ], 404);
    }
}
