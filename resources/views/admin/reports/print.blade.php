@php
  $reportTitle = match ($activeTab) {
      'faculty' => 'Complete List of Faculty',
      'students' => 'Complete List of Students',
      'sections' => 'Complete List of Sections',
      default => 'Complete List of Events',
  };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $reportTitle }} - Print</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 24px;
      color: #1f2937;
      background: #ffffff;
    }

    .report-header {
      margin-bottom: 16px;
    }

    .report-header h2 {
      margin: 0;
      font-size: 24px;
      color: #102a56;
    }

    .report-header p {
      margin: 6px 0 0;
      color: #4b5563;
      font-size: 13px;
    }

    .print-actions {
      display: flex;
      gap: 8px;
      margin-bottom: 14px;
    }

    .print-actions button {
      border: 1px solid #102a56;
      background: #102a56;
      color: #ffffff;
      border-radius: 6px;
      padding: 7px 12px;
      cursor: pointer;
      font-size: 13px;
    }

    .print-actions button.secondary {
      background: #ffffff;
      color: #102a56;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #d6deeb;
      font-size: 14px;
    }

    thead th {
      background: #1c2f59;
      color: #ffffff;
      text-align: left;
      padding: 10px 12px;
      border: 1px solid #1c2f59;
    }

    tbody td {
      border: 1px solid #d6deeb;
      padding: 9px 12px;
      vertical-align: top;
    }

    tbody tr:nth-child(even) {
      background: #f8fafc;
    }

    .group-row td {
      background: #eaf1ff !important;
      color: #102a56;
      font-weight: 700;
    }

    .empty-row td {
      text-align: center;
      color: #6b7280;
      padding: 18px;
    }

    @media print {
      body {
        margin: 8mm;
      }

      .print-actions {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="print-actions">
    <button type="button" onclick="window.print()">Print</button>
    <button type="button" class="secondary" onclick="window.close()">Close</button>
  </div>

  <div class="report-header">
    <h2>{{ $reportTitle }}</h2>
    <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
  </div>

  @if($activeTab === 'faculty')
    <table>
      <thead>
        <tr>
          <th style="width: 8%;">#</th>
          <th style="width: 36%;">Name</th>
          <th style="width: 28%;">Department</th>
          <th style="width: 28%;">Position</th>
        </tr>
      </thead>
      <tbody>
        @if($faculties->isEmpty())
          <tr class="empty-row"><td colspan="4">No faculty records found.</td></tr>
        @else
          @php
            $rowNumber = 1;
            $groupedFaculties = $faculties->groupBy(function ($faculty) {
                return $faculty->department ?: 'Not Assigned';
            });
          @endphp
          @foreach($groupedFaculties as $department => $departmentFaculties)
            <tr class="group-row">
              <td colspan="4">Department: {{ $department }}</td>
            </tr>
            @foreach($departmentFaculties as $faculty)
              <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ trim($faculty->first_name . ' ' . $faculty->middle_name . ' ' . $faculty->last_name) }}</td>
                <td>{{ $faculty->department ?? 'Not Assigned' }}</td>
                <td>{{ $faculty->facultyPosition->name ?? 'Not Assigned' }}</td>
              </tr>
              @php
                $rowNumber++;
              @endphp
            @endforeach
          @endforeach
        @endif
      </tbody>
    </table>
  @elseif($activeTab === 'students')
    <table>
      <thead>
        <tr>
          <th style="width: 8%;">#</th>
          <th style="width: 18%;">LRN</th>
          <th style="width: 31%;">Name</th>
          <th style="width: 23%;">Section</th>
          <th style="width: 20%;">Grade Level</th>
        </tr>
      </thead>
      <tbody>
        @if($students->isEmpty())
          <tr class="empty-row"><td colspan="5">No student records found.</td></tr>
        @else
          @php
            $rowNumber = 1;
            $groupedStudents = $students->groupBy(function ($student) {
                $gradeLevel = $student->section->grade_level ?? null;
                if (!$gradeLevel) {
                    return 'No Grade Assigned';
                }

                return preg_match('/(\d+)/', (string) $gradeLevel, $gradeMatch)
                    ? 'Grade ' . $gradeMatch[1]
                    : (string) $gradeLevel;
            });
          @endphp
          @foreach($groupedStudents as $gradeLevel => $gradeStudents)
            <tr class="group-row">
              <td colspan="5">Grade Level: {{ $gradeLevel }}</td>
            </tr>
            @foreach($gradeStudents as $student)
              @php
                $studentGrade = $student->section->grade_level ?? null;
                $studentGradeLabel = $studentGrade
                    ? (preg_match('/(\d+)/', (string) $studentGrade, $gradeMatch)
                        ? 'Grade ' . $gradeMatch[1]
                        : (string) $studentGrade)
                    : 'N/A';
              @endphp
              <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $student->lrn ?? 'N/A' }}</td>
                <td>{{ trim($student->last_name . ', ' . $student->first_name . ' ' . $student->middle_name) }}</td>
                <td>{{ $student->section->name ?? 'Not Assigned' }}</td>
                <td>{{ $studentGradeLabel }}</td>
              </tr>
              @php
                $rowNumber++;
              @endphp
            @endforeach
          @endforeach
        @endif
      </tbody>
    </table>
  @elseif($activeTab === 'sections')
    <table>
      <thead>
        <tr>
          <th style="width: 8%;">#</th>
          <th style="width: 28%;">Section</th>
          <th style="width: 20%;">Grade Level</th>
          <th style="width: 28%;">Adviser</th>
          <th style="width: 16%;">Students</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sections as $section)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $section->name }}</td>
            <td>{{ preg_match('/(\d+)/', (string) $section->grade_level, $gradeMatch) ? 'Grade ' . $gradeMatch[1] : ($section->grade_level ?? 'N/A') }}</td>
            <td>{{ $section->adviser ? trim($section->adviser->first_name . ' ' . $section->adviser->last_name) : 'Not Assigned' }}</td>
            <td>{{ $section->students_count }}</td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5">No section records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  @else
    <table>
      <thead>
        <tr>
          <th style="width: 8%;">#</th>
          <th style="width: 32%;">Title</th>
          <th style="width: 16%;">Type</th>
          <th style="width: 22%;">Start Date</th>
          <th style="width: 22%;">End Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($events as $event)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $event->title }}</td>
            <td>{{ $event->type_name }}</td>
            <td>{{ $event->start_date ? $event->start_date->format('M d, Y h:i A') : 'N/A' }}</td>
            <td>{{ $event->end_date ? $event->end_date->format('M d, Y h:i A') : '-' }}</td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5">No event records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  @endif

  <script>
    window.addEventListener('load', function () {
      window.print();
    });
  </script>
</body>
</html>
