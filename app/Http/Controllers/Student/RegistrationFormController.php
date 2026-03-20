<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class RegistrationFormController extends Controller
{
    /**
     * Generate and download the student registration form PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        $user = Auth::user();

        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Unauthorized access');
        }

        $pdf = $this->generatePdf($user);
        
        // Generate filename
        $filename = 'Registration_Form_' . ($user->custom_id ?? 'Student') . '_' . date('Y-m-d') . '.pdf';

        // Return the PDF as a download
        return response($pdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Preview the registration form in browser.
     *
     * @return \Illuminate\Http\Response
     */
    public function preview()
    {
        $user = Auth::user();

        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Unauthorized access');
        }

        $pdf = $this->generatePdf($user);

        // Return the PDF for viewing in browser
        return response($pdf->Output('Registration_Form_Preview.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Registration_Form_Preview.pdf"');
    }

    /**
     * Generate the PDF document.
     *
     * @param \App\Models\User $user
     * @return TCPDF
     */
    protected function generatePdf($user)
    {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Amore Academy');
        $pdf->SetAuthor('Amore Academy');
        $pdf->SetTitle('Student Registration Form');
        $pdf->SetSubject('Registration Form');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // School Year
        $schoolYear = date('Y') . '-' . (date('Y') + 1);
        $generatedDate = now()->format('F d, Y');

        // Build HTML content
        $html = $this->buildPdfHtml($user, $schoolYear, $generatedDate);

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf;
    }

    /**
     * Build the HTML content for the PDF.
     *
     * @param \App\Models\User $user
     * @param string $schoolYear
     * @param string $generatedDate
     * @return string
     */
    protected function buildPdfHtml($user, $schoolYear, $generatedDate)
    {
        $fullName = trim(($user->last_name ?? '') . ', ' . ($user->first_name ?? '') . ' ' . ($user->middle_name ?? ''));
        
        // Get section info
        $section = $user->section;
        $sectionName = $section ? $section->name : 'N/A';
        
        // Get student's address from admission record if exists
        $admission = \App\Models\Admission::where('user_id', $user->id)->first();
        $address = $admission ? strtoupper($admission->address ?? '') : '';
        
        // Get subjects with teachers for this student's section
        $subjectsHtml = $this->buildSubjectsTableHtml($section);
        
        return '
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 10pt; 
                color: #000000; 
            }
            .header { 
                text-align: center; 
                margin-bottom: 15px; 
                line-height: 0.5;
            }
            .header h2 { 
                margin: 0; 
                padding: 0;
                font-size: 14pt; 
            }
            
            .header h3 { 
                margin: 0; 
                padding: 0;
                font-size: 11pt; 
                font-weight: normal;
            }
            
            .header-line {
                line-height: 1;
            } 
            .form-title {
                font-size: 14pt;
                font-weight: bold;
                margin-top: 20px;
            }
            .label { 
                font-weight: bold; 
            }
            .info-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 10px;
            }
            .info-table td { 
                padding: 5px 8px; 
                font-size: 10pt;
                vertical-align: top;
            }
            .subjects-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px;
            }
            .subjects-table th { 
                background-color: #e6e6e6; 
                border: 1px solid #000; 
                padding: 8px; 
                font-size: 10pt;
                text-align: left;
            }
            .subjects-table td { 
                border: 1px solid #000; 
                padding: 8px; 
                font-size: 10pt;
            }
            .signature-table { 
                width: 100%; 
                margin-top: 400px; 
            }
            .signature-table td { 
                width: 50%; 
                text-align: center; 
                padding: 10px 30px;
                vertical-align: bottom;
            }
            .sig-line {
                border-top: 1px solid #000;
                padding-top: 5px;
                font-size: 9pt;
            }
        </style>

        <!-- Header -->
        <div class="header header-line">
            <h3>Republic of the Philippines</h3>
            <h2>Amore Academy</h2>
            <h3>Trece Martires City Campus</h3>
            <div class="form-title">REGISTRATION FORM</div>
        </div>

        <!-- Student Information Grid -->
        <table class="info-table">
            <tr>
                <td width="33%"><span class="label">Name:</span> ' . $fullName . '</td>
                <td width="33%"><span class="label">Student ID:</span> ' . ($user->custom_id ?? 'N/A') . '</td>
                <td width="34%"><span class="label">Date:</span> ' . $generatedDate . '</td>
            </tr>
            <tr>
                <td><span class="label">Grade Level:</span> ' . ($user->grade_level ?? 'N/A') . '</td>
                <td><span class="label">LRN:</span> ' . ($user->lrn ?? 'N/A') . '</td>
                <td><span class="label">School Year:</span> ' . $schoolYear . '</td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">Address:</span> ' . $address . '</td>
                <td><span class="label">Section:</span> ' . $sectionName . '</td>
            </tr>
        </table>

        <br><br><br>

        <!-- Subjects Table -->
        ' . $subjectsHtml . '

        <br><br><br><br><br><br><br><br><br><br>

        <!-- Signature Section -->
        <table class="signature-table">
            <tr>
                <td>
                    <br><br><br>
                    <div class="sig-line">Student\'s Signature</div>
                </td>
                <td>
                    <br><br><br>
                    <div class="sig-line">OIC - Office of the Campus Registrar\'s Signature</div>
                </td>
            </tr>
        </table>
        ';
    }

    /**
     * Build the subjects table HTML.
     *
     * @param \App\Models\Section|null $section
     * @return string
     */
    protected function buildSubjectsTableHtml($section)
    {
        $html = '
        <table class="subjects-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th style="width: 20%;">Subject</th>
                    <th style="width: 47%;">Subject Description</th>
                    <th style="width: 25%;">Teacher</th>
                </tr>
            </thead>
            <tbody>';

        if ($section) {
            $subjectTeachers = $section->subjectTeachers()->with(['subject', 'teacher'])->get();
            
            if ($subjectTeachers->count() > 0) {
                $counter = 1;
                foreach ($subjectTeachers as $st) {
                    $subjectName = $st->subject ? $st->subject->name : 'N/A';
                    $subjectDesc = $st->subject ? ($st->subject->description ?? '') : '';
                    $teacherName = $st->teacher 
                        ? ($st->teacher->last_name ?? '') . ', ' . substr($st->teacher->first_name ?? '', 0, 1) . '.'
                        : 'TBA';
                    
                    $html .= '
                <tr>
                    <td style="text-align: center;">' . $counter . '</td>
                    <td>' . htmlspecialchars($subjectName) . '</td>
                    <td>' . htmlspecialchars($subjectDesc) . '</td>
                    <td>' . htmlspecialchars($teacherName) . '</td>
                </tr>';
                    $counter++;
                }
            } else {
                $html .= '
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #666;">No subjects assigned yet</td>
                </tr>';
            }
        } else {
            $html .= '
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #666;">No section assigned yet</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>';

        return $html;
    }
}
