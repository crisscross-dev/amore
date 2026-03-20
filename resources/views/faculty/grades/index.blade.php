@extends('layouts.app')

@section('title', 'Manage Grades - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.faculty-sidebar')

      <main class="col-lg-9 col-md-8">
        <div class="welcome-card">
          <div class="row g-4 align-items-center">
            <div class="col-lg-7">
              <h4 class="mb-2">
                <i class="fas fa-graduation-cap me-2"></i>
                Grade Management
              </h4>
              <p class="mb-0 opacity-90">Select a subject and section, then enter grades for your assigned students.</p>
            </div>
            <div class="col-lg-5">
              <div class="quick-link-card">
                <span class="text-uppercase small fw-semibold text-white-50">Quick actions</span>
                <a href="{{ route('faculty.grades.import.create') }}">
                  <i class="fas fa-file-import"></i>
                  Import Grades
                </a>
              </div>
            </div>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0 mt-2">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="faculty-management-card faculty-management-table">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-success">
              <i class="fas fa-table me-2 text-success"></i>
              Subject Grade Entry
            </h5>
            <span class="badge bg-success bg-opacity-75">{{ $subjects->count() }} subject{{ $subjects->count() === 1 ? '' : 's' }}</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Sections</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($subjects as $subject)
                  @php
                    $detailId = 'grade-details-' . $subject->id;
                    $allowedSections = $subjectSectionMap[$subject->id] ?? [];
                  @endphp
                  <tr>
                    <td>
                      <button
                        type="button"
                        class="btn btn-link p-0 text-success fw-semibold text-decoration-none"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $detailId }}"
                        aria-expanded="false"
                        aria-controls="{{ $detailId }}"
                      >
                        {{ $subject->name }}
                      </button>
                      <small class="text-muted d-block">Select a section to enter grades.</small>
                    </td>
                    <td>
                      @if(empty($allowedSections))
                        <span class="text-muted">All assigned sections</span>
                      @else
                        {{ count($allowedSections) }} section{{ count($allowedSections) === 1 ? '' : 's' }}
                      @endif
                    </td>
                    <td class="text-end">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-success"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $detailId }}"
                        aria-expanded="false"
                        aria-controls="{{ $detailId }}"
                      >
                        <i class="fas fa-pen"></i>
                      </button>
                    </td>
                  </tr>
                  <tr class="grade-detail-row">
                    <td colspan="3" class="p-0 border-0">
                      <div class="collapse" id="{{ $detailId }}">
                        <div class="p-3">
                          <form action="{{ route('faculty.grades.store') }}" method="POST" class="row g-3" data-grade-subject>
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <div class="col-md-6">
                              <label class="form-label">Grade Level</label>
                              <select class="form-select" required data-grade-select>
                                <option value="" selected disabled>Select grade level</option>
                                @foreach($gradeLevels as $gradeLevel)
                                  <option value="{{ $gradeLevel }}">{{ $gradeLevel }}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Section</label>
                              <select name="section_id" class="form-select" required disabled data-section-select data-allowed-sections="{{ implode(',', $allowedSections) }}">
                                <option value="" selected disabled>Select section</option>
                                @foreach($sections as $section)
                                  <option value="{{ $section->id }}" data-grade-level="{{ $section->grade_level }}">
                                    {{ $section->name }}
                                  </option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Term</label>
                              <input type="text" name="term" class="form-control" placeholder="e.g., Q1" required>
                            </div>
                            <div class="col-12" data-students-wrapper style="display: none;">
                              <label class="form-label">Students</label>
                              <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                  <thead>
                                    <tr>
                                      <th>Student</th>
                                      <th style="width: 180px;">Grade</th>
                                    </tr>
                                  </thead>
                                  <tbody data-students-body>
                                    @foreach($students as $student)
                                      <tr data-section="{{ $student->section_id }}" style="display: none;">
                                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td>
                                          <input
                                            type="number"
                                            step="1"
                                            min="50"
                                            max="100"
                                            name="grade_values[{{ $student->id }}]"
                                            class="form-control"
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
                            </div>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3">
                      <div class="faculty-management-empty">
                        <i class="fas fa-graduation-cap"></i>
                        <h5 class="fw-semibold mb-2 text-success">No subjects assigned</h5>
                        <p class="mb-0">Assign subjects to start entering grades.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-grade-subject]').forEach(function (form) {
              var gradeSelect = form.querySelector('[data-grade-select]');
              var sectionSelect = form.querySelector('[data-section-select]');
              var studentsWrapper = form.querySelector('[data-students-wrapper]');
              var studentsBody = form.querySelector('[data-students-body]');

              if (!gradeSelect || !sectionSelect || !studentsWrapper || !studentsBody) {
                return;
              }

              var rawAllowed = sectionSelect.getAttribute('data-allowed-sections') || '';
              var allowedSections = rawAllowed ? rawAllowed.split(',') : [];

              function setSectionEnabled(enabled) {
                sectionSelect.disabled = !enabled;
                if (!enabled) {
                  sectionSelect.value = '';
                }
              }

              function filterSections() {
                var gradeLevel = gradeSelect.value;
                var options = sectionSelect.querySelectorAll('option');
                options.forEach(function (option) {
                  if (!option.value) return;
                  var matchesAllowed = allowedSections.length === 0 || allowedSections.indexOf(option.value) !== -1;
                  var matchesGrade = !gradeLevel || option.getAttribute('data-grade-level') === gradeLevel;
                  option.hidden = !(matchesAllowed && matchesGrade);
                });

                setSectionEnabled(!!gradeLevel);
                if (sectionSelect.selectedOptions.length && sectionSelect.selectedOptions[0].hidden) {
                  sectionSelect.value = '';
                }
                filterStudents();
              }

              function filterStudents() {
                var sectionId = sectionSelect.value;
                var rows = studentsBody.querySelectorAll('tr');
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

              gradeSelect.addEventListener('change', filterSections);
              sectionSelect.addEventListener('change', filterStudents);
              filterSections();
            });
          });
        </script>
      </main>
    </div>
  </div>
</div>
@endsection
