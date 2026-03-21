<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form - {{ $schoolName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px 30px;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }

        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18pt;
            color: #2c3e50;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 14pt;
            color: #34495e;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #7f8c8d;
        }

        /* Form Title */
        .form-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* School Year */
        .school-year {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .school-year strong {
            color: #2c3e50;
        }

        /* Section Title */
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 10px 0;
            border-radius: 3px;
        }

        /* Form Table */
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .form-table td {
            padding: 8px 10px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }

        .form-table .label {
            background-color: #ecf0f1;
            font-weight: bold;
            width: 30%;
            font-size: 10pt;
        }

        .form-table .value {
            width: 70%;
            background-color: #fff;
        }

        .form-table .value-empty {
            min-height: 20px;
            border-bottom: 1px dotted #95a5a6;
        }

        /* Two Column Layout */
        .two-column {
            width: 100%;
            border-collapse: collapse;
        }

        .two-column td {
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }

        .two-column .form-table {
            margin: 0;
        }

        /* Photo Box */
        .photo-box {
            width: 120px;
            height: 150px;
            border: 2px solid #bdc3c7;
            float: right;
            margin-left: 15px;
            margin-bottom: 15px;
            text-align: center;
            padding-top: 60px;
            font-size: 9pt;
            color: #95a5a6;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            padding: 20px 30px;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 40px;
            font-size: 10pt;
        }

        .signature-date {
            font-size: 9pt;
            color: #7f8c8d;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #95a5a6;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }

        /* Checkbox Group */
        .checkbox-group {
            margin: 5px 0;
        }

        .checkbox {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #333;
            margin-right: 5px;
            vertical-align: middle;
        }

        .checkbox.checked {
            background-color: #2c3e50;
        }

        /* Instructions */
        .instructions {
            background-color: #fff9e6;
            border: 1px solid #f1c40f;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 9pt;
            border-radius: 3px;
        }

        .instructions strong {
            color: #d35400;
        }

        /* Pre-filled Value */
        .pre-filled {
            color: #2c3e50;
            font-weight: 500;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('SchoolLogo.png') }}" alt="School Logo">
            <h1>{{ $schoolName }}</h1>
            <h2>Student Registration Form</h2>
            <p>{{ $schoolAddress }}</p>
        </div>

        <!-- School Year -->
        <div class="school-year">
            <strong>School Year: {{ $schoolYear }}</strong>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <strong>Instructions:</strong> Please fill out this form completely and accurately. 
            All fields marked with an asterisk (*) are required. Submit this form along with the required documents to the Registrar's Office.
        </div>

        <!-- Student Information Section -->
        <div class="section-title">
            <i class="fas fa-user"></i> Student Information
        </div>

        <div class="clearfix">
            <div class="photo-box">
                2x2 ID Photo<br>(Recent)
            </div>

            <table class="form-table">
                <tr>
                    <td class="label">Student ID *</td>
                    <td class="value"><span class="pre-filled">{{ $student->custom_id ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="label">LRN (Learner Reference Number) *</td>
                    <td class="value"><span class="pre-filled">{{ $student->lrn ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Last Name *</td>
                    <td class="value"><span class="pre-filled">{{ $student->last_name ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="label">First Name *</td>
                    <td class="value"><span class="pre-filled">{{ $student->first_name ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Middle Name</td>
                    <td class="value"><span class="pre-filled">{{ $student->middle_name ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Grade Level *</td>
                    <td class="value"><span class="pre-filled">{{ $student->grade_level ?? '' }}</span></td>
                </tr>
            </table>
        </div>

        <!-- Contact Information Section -->
        <div class="section-title">
            Contact Information
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Email Address *</td>
                <td class="value"><span class="pre-filled">{{ $student->email ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="label">Contact Number *</td>
                <td class="value"><span class="pre-filled">{{ $student->contact_number ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="label">Complete Address *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
        </table>

        <!-- Guardian/Parent Information Section -->
        <div class="section-title">
            Parent/Guardian Information
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Parent/Guardian Name *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Relationship to Student *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Contact Number *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Email Address</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Occupation</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
        </table>

        <!-- Emergency Contact Section -->
        <div class="section-title">
            Emergency Contact
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Emergency Contact Name *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Relationship *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Contact Number *</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
        </table>

        <!-- Previous School Information -->
        <div class="section-title">
            Previous School Information
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Name of Previous School</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">School Address</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Last Grade Level Completed</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">School Year</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
        </table>

        <!-- Required Documents Checklist -->
        <div class="section-title">
            Required Documents Checklist
        </div>

        <table class="form-table">
            <tr>
                <td colspan="2" style="padding: 15px;">
                    <div class="checkbox-group">
                        <span class="checkbox"></span> PSA Birth Certificate (Original and Photocopy)
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox"></span> Report Card / Form 138
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox"></span> Certificate of Good Moral Character
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox"></span> 2x2 ID Photos (4 pcs)
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox"></span> Certificate of Transfer / Form 137 (for transferees)
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox"></span> Medical Certificate
                    </div>
                </td>
            </tr>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line">
                            Student's Signature over Printed Name
                        </div>
                        <div class="signature-date">Date: _______________</div>
                    </td>
                    <td>
                        <div class="signature-line">
                            Parent/Guardian's Signature over Printed Name
                        </div>
                        <div class="signature-date">Date: _______________</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Official Use Section -->
        <div class="section-title" style="background-color: #7f8c8d;">
            For Official Use Only
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Assessed By</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Assessment Date</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Approved By</td>
                <td class="value"><div class="value-empty"></div></td>
            </tr>
            <tr>
                <td class="label">Remarks</td>
                <td class="value"><div class="value-empty" style="min-height: 40px;"></div></td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>This form was generated on {{ $generatedDate }}</p>
            <p>&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

