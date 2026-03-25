@extends('layouts.app-student')

@section('title', 'My Grades - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css','resources/css/student/grade-view.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        <div class="welcome-card mb-4">
          <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>My Approved Grades</h4>
        </div>

        <div class="faculty-management-card faculty-management-table">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Term</th>
                  <th>Grade</th>
                  <th>Approved At</th>
                </tr>
              </thead>
              <tbody>
                @forelse($entries as $entry)
                <tr>
                  <td>{{ $entry->subject->name ?? 'Subject' }}</td>
                  <td>{{ $entry->term }}</td>
                  <td>{{ number_format($entry->grade_value, 2) }}</td>
                  <td>{{ optional($entry->approved_at)->format('M d, Y h:i A') ?? 'Pending admin approval' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="4">
                    <div class="faculty-management-empty">
                      <i class="fas fa-info-circle"></i>
                      <h5 class="fw-semibold mb-2">No approved grades yet</h5>
                      <p class="mb-0">Approved grades will appear here once available.</p>
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