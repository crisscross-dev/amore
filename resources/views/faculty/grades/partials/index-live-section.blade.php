<div class="faculty-management-card faculty-management-table" id="facultyManageGradesLiveSection" data-live-url="{{ route('faculty.grades.live-section') }}">
  @php
  $mapehComponentNamesByGrade = [
  7 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
  8 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
  9 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
  10 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
  ];

  $groupedAssignments = $assignments
  ->groupBy(function ($assignment) use ($mapehComponentNamesByGrade) {
  $gradeNumber = null;
  if (preg_match('/(7|8|9|10|11|12)/', (string) (optional($assignment->section)->grade_level ?? ''), $gradeMatch)) {
  $gradeNumber = (int) $gradeMatch[1];
  }

  $subjectName = (string) (optional($assignment->subject)->name ?? '');
  $normalizedSubjectName = mb_strtoupper($subjectName, 'UTF-8');
  $mapehNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
  $normalizedSubject = !empty($mapehNames) && in_array($normalizedSubjectName, $mapehNames, true)
  ? 'MAPEH'
  : strtolower(trim($subjectName ?: 'n/a'));

  return ((int) $assignment->section_id) . '|' . $normalizedSubject;
  })
  ->map(function ($items) use ($mapehComponentNamesByGrade) {
  $representative = $items->first(function ($assignment) {
  return (bool) (optional($assignment)->day_of_week || optional($assignment)->start_time || optional($assignment)->room);
  }) ?? $items->first();

  $gradeNumber = null;
  if (preg_match('/(7|8|9|10|11|12)/', (string) (optional($representative->section)->grade_level ?? ''), $gradeMatch)) {
  $gradeNumber = (int) $gradeMatch[1];
  }

  $mapehNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
  $isMapeh = $items->contains(function ($assignment) use ($mapehNames) {
  $name = (string) (optional($assignment->subject)->name ?? '');
  return !empty($mapehNames) && in_array(mb_strtoupper($name, 'UTF-8'), $mapehNames, true);
  });

  return (object) [
  'assignment' => $representative,
  'subject_name' => $isMapeh ? 'MAPEH' : (optional($representative->subject)->name ?? 'N/A'),
  ];
  })
  ->values();
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-success">
      <i class="fas fa-table me-2 text-success"></i>
      Subject Grade Entry
    </h5>
    <span class="badge bg-success bg-opacity-75">{{ $groupedAssignments->count() }} assignment{{ $groupedAssignments->count() === 1 ? '' : 's' }}</span>
  </div>

  <div class="grade-cards-wrap">
    @forelse($groupedAssignments as $grouped)
    @php
    $assignment = $grouped->assignment;
    $section = $assignment->section;
    $studentsTotal = (int) ($studentCounts[$assignment->section_id] ?? 0);
    @endphp
    <a href="{{ route('faculty.grades.assignment', $assignment) }}" class="grade-card">
      <div class="grade-card-preview">
        <div class="card-head">
          <div>
            <div class="hierarchy-label">Subject</div>
            <div class="hierarchy-value">{{ $grouped->subject_name }}</div>
          </div>
          <div class="text-md-end">
            <div class="hierarchy-label">Section</div>
            <div class="hierarchy-value">{{ $section->name ?? 'N/A' }}</div>
          </div>
        </div>

        <div class="meta-item">
          <div class="d-flex flex-wrap gap-3">
            <span><strong>Grade Level:</strong> {{ $section->grade_level ?? 'N/A' }}</span>
            <span><strong>Students:</strong> {{ $studentsTotal }}</span>
            <span><strong>Schedule:</strong>
              @if($assignment->day_of_week && $assignment->start_time && $assignment->end_time)
              {{ $assignment->day_of_week }}, {{ substr($assignment->start_time, 0, 5) }} - {{ substr($assignment->end_time, 0, 5) }}
              @else
              TBA
              @endif
            </span>
            <span><strong>Room:</strong> {{ $assignment->room ?: 'TBA' }}</span>
          </div>
        </div>

      </div>
    </a>
    @empty
    <div class="faculty-management-empty">
      <i class="fas fa-graduation-cap"></i>
      <h5 class="fw-semibold mb-2 text-success">No subject assignments found</h5>
      <p class="mb-0">Ask admin to assign a section and subject to start grading.</p>
    </div>
    @endforelse
  </div>
</div>
