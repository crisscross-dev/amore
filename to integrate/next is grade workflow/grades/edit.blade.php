@extends('layouts.app')

@section('title', 'Edit Grade - Faculty Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.faculty-sidebar')
      <main class="col-lg-9 col-md-8">
        <div class="welcome-card mb-4">
          <h4 class="mb-0"><i class="fas fa-pen me-2"></i>Edit Grade Entry</h4>
        </div>

        <div class="faculty-management-card p-4">
          <form action="{{ route('faculty.grades.update', $grade) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select name="section_id" id="sectionSelect" class="form-select" required>
                <option value="" disabled>Select section</option>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ (int) optional($grade->student)->section_id === $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Student</label>
              <select name="student_id" id="studentSelect" class="form-select" required>
                @foreach($students as $student)
                  <option value="{{ $student->id }}" data-section="{{ $student->section_id }}" {{ (int) $grade->student_id === $student->id ? 'selected' : '' }}>
                    {{ $student->last_name }}, {{ $student->first_name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Term</label>
              <input type="text" name="term" class="form-control" value="{{ $grade->term }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <select name="subject_id" class="form-select" required>
                @foreach($subjects as $subject)
                  <option value="{{ $subject->id }}" {{ $grade->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Grade Value</label>
              <input type="number" step="0.01" min="0" max="100" name="grade_value" class="form-control" value="{{ $grade->grade_value }}" required>
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
    var studentSelect = document.getElementById('studentSelect');

    function filterStudents() {
      var sectionId = sectionSelect.value;
      var options = studentSelect.querySelectorAll('option');
      options.forEach(function (option) {
        if (!option.value) return;
        var matches = option.getAttribute('data-section') === sectionId;
        option.hidden = !matches;
      });
    }

    sectionSelect.addEventListener('change', filterStudents);
    if (sectionSelect.value) {
      filterStudents();
    }
  });
</script>
@endsection