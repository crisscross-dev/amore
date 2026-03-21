@extends('layouts.app')

@section('title', 'Edit Grade - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        <div class="welcome-card mb-4">
          <h4 class="mb-0"><i class="fas fa-pen me-2"></i>Edit Grade Entry</h4>
        </div>

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="faculty-management-card p-4">
          <form action="{{ route('faculty.grades.update', $grade) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <select id="subjectSelect" class="form-select" disabled>
                @foreach($subjects as $subject)
                  @php
                    $sectionIds = $subjectSectionMap[$subject->id] ?? [];
                  @endphp
                  <option value="{{ $subject->id }}" data-section-ids="{{ implode(',', $sectionIds) }}" {{ (int) $selectedSubjectId === $subject->id ? 'selected' : '' }}>
                    {{ $subject->name }}
                  </option>
                @endforeach
              </select>
              <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select id="sectionSelect" class="form-select" disabled>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ (int) $selectedSectionId === $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                  </option>
                @endforeach
              </select>
              <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Term</label>
              <input type="text" name="term" class="form-control" value="{{ old('term', $selectedTerm) }}" readonly>
            </div>
            <div class="col-12" id="studentsWrapper">
              <label class="form-label">Students</label>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Student</th>
                      <th style="width: 180px;">Grade</th>
                    </tr>
                  </thead>
                  <tbody id="studentsTableBody">
                    @foreach($students as $student)
                      <tr data-section="{{ $student->section_id }}" style="display: none;">
                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                        <td>
                          <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            name="grade_values[{{ $student->id }}]"
                            class="form-control"
                            value="{{ old('grade_values.' . $student->id, $gradeValues[$student->id] ?? '') }}"
                          >
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-12 d-flex gap-2">
              <button class="btn btn-green"><i class="fas fa-save me-2"></i>Save Changes</button>
              <a href="{{ route('faculty.grades.index') }}" class="btn btn-outline-light">Cancel</a>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var sectionSelect = document.getElementById('sectionSelect');
    var subjectSelect = document.getElementById('subjectSelect');
    var studentsWrapper = document.getElementById('studentsWrapper');
    var studentsTableBody = document.getElementById('studentsTableBody');

    function filterStudents() {
      var sectionId = sectionSelect.value;
      var rows = studentsTableBody.querySelectorAll('tr');
      var visibleCount = 0;
      rows.forEach(function (row) {
        var matches = row.getAttribute('data-section') === sectionId;
        row.style.display = matches ? '' : 'none';
        if (matches) {
          visibleCount += 1;
        }
      });

      studentsWrapper.style.display = sectionId ? '' : 'none';
      if (sectionId && visibleCount === 0) {
        studentsWrapper.style.display = 'none';
      }
    }

    filterStudents();
  });
</script>
@endsection
