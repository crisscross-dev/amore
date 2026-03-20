@extends('layouts.app')

@section('title', 'New Grade - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.faculty-sidebar')
      <main class="col-lg-9 col-md-8">
        <div class="welcome-card mb-4">
          <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Create Grade Entry</h4>
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
          <form action="{{ route('faculty.grades.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <select name="subject_id" id="subjectSelect" class="form-select" required>
                <option value="" disabled selected>Select subject</option>
                @foreach($subjects as $subject)
                  @php
                    $sectionIds = $subjectSectionMap[$subject->id] ?? [];
                  @endphp
                  <option value="{{ $subject->id }}" data-section-ids="{{ implode(',', $sectionIds) }}" {{ (int) old('subject_id') === $subject->id ? 'selected' : '' }}>
                    {{ $subject->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select name="section_id" id="sectionSelect" class="form-select" required disabled>
                <option value="" disabled selected>Select section</option>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ (int) old('section_id') === $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Term</label>
              <input type="text" name="term" class="form-control" placeholder="e.g., Q1" value="{{ old('term') }}" required>
            </div>
            <div class="col-12" id="studentsWrapper" style="display: none;">
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
                            value="{{ old('grade_values.' . $student->id) }}"
                          >
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-12 d-flex gap-2">
              <button class="btn btn-green"><i class="fas fa-save me-2"></i>Save Draft</button>
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
    var subjectSelect = document.getElementById('subjectSelect');
    var sectionSelect = document.getElementById('sectionSelect');
    var studentsWrapper = document.getElementById('studentsWrapper');
    var studentsTableBody = document.getElementById('studentsTableBody');

    function setSelectEnabled(select, enabled) {
      select.disabled = !enabled;
      if (!enabled) {
        select.value = '';
      }
    }

    function filterSections() {
      var subjectOption = subjectSelect.selectedOptions[0];
      var raw = subjectOption ? subjectOption.getAttribute('data-section-ids') || '' : '';
      var allowed = raw ? raw.split(',') : [];

      var options = sectionSelect.querySelectorAll('option');
      options.forEach(function (option) {
        if (!option.value) return;
        if (!subjectSelect.value) {
          option.hidden = true;
          return;
        }
        if (allowed.length === 0) {
          option.hidden = false;
          return;
        }
        option.hidden = allowed.indexOf(option.value) === -1;
      });

      setSelectEnabled(sectionSelect, !!subjectSelect.value);
      if (sectionSelect.selectedOptions.length && sectionSelect.selectedOptions[0].hidden) {
        sectionSelect.value = '';
      }
      filterStudents();
    }

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

    subjectSelect.addEventListener('change', filterSections);
    sectionSelect.addEventListener('change', filterStudents);

    if (subjectSelect.value) {
      filterSections();
    }
  });
</script>
@endsection