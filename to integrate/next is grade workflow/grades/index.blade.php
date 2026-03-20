@extends('layouts.app')

@section('title', 'Manage Grades - Faculty Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.faculty-sidebar')

      <main class="col-lg-9 col-md-8">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-graduation-cap me-2"></i>Manage Grades</h4>
              <p class="mb-0 opacity-90">Create drafts and submit grades for approval.</p>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('faculty.grades.import.create') }}" class="btn btn-outline-light"><i class="fas fa-file-import me-2"></i>Import Grades</a>
              <a href="{{ route('faculty.grades.create') }}" class="btn btn-green"><i class="fas fa-plus me-2"></i>New Grade</a>
            </div>
          </div>
        </div>

        <div class="faculty-management-card mb-4">
          <form method="GET" action="{{ route('faculty.grades.index') }}" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Term</label>
              <input type="text" name="term" value="{{ $filters['term'] ?? '' }}" class="form-control" placeholder="e.g., Q1">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All</option>
                @foreach(['draft','submitted','approved','rejected'] as $s)
                  <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button class="btn btn-green w-100"><i class="fas fa-filter me-2"></i>Apply Filters</button>
            </div>
          </form>
        </div>

        <div class="faculty-management-card faculty-management-table">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-table me-2 text-success"></i>Grade Entries</h5>
            <span class="badge bg-success bg-opacity-75">{{ $entries->total() }} entries</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Subject</th>
                  <th>Term</th>
                  <th>Grade</th>
                  <th>Status</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($entries as $entry)
                  <tr>
                    <td>{{ $entry->student->first_name ?? 'Student' }} {{ $entry->student->last_name ?? '' }}</td>
                    <td>{{ $entry->subject->name ?? 'Subject' }}</td>
                    <td>{{ $entry->term }}</td>
                    <td>{{ number_format($entry->grade_value, 2) }}</td>
                    <td class="text-capitalize">{{ $entry->status }}</td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm" role="group">
                        @if($entry->status === 'draft')
                          <a href="{{ route('faculty.grades.edit', $entry) }}" class="btn btn-outline-success"><i class="fas fa-pen"></i></a>
                          <form action="{{ route('faculty.grades.submit', $entry) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Submit this grade for approval?');">
                              <i class="fas fa-paper-plane"></i>
                            </button>
                          </form>
                          <form action="{{ route('faculty.grades.destroy', $entry) }}" method="POST" onsubmit="return confirm('Delete this draft grade?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                          </form>
                        @else
                          <a class="btn btn-outline-secondary disabled" aria-disabled="true"><i class="fas fa-eye"></i></a>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6">
                      <div class="faculty-management-empty">
                        <i class="fas fa-graduation-cap"></i>
                        <h5 class="fw-semibold mb-2">No grade entries yet</h5>
                        <p class="mb-0">Create your first grade entry and submit for approval.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-4">{{ $entries->links() }}</div>
        </div>
      </main>
    </div>
  </div>
</div>
@endsection
