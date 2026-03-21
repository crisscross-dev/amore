@extends('layouts.app')

@section('title', 'Import Result - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

@php($summary = $summary ?? session('import_summary'))

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-clipboard-check me-2"></i>Import Summary</h4>
              <p class="mb-0 opacity-90">Review results of the recent import.</p>
            </div>
            <a href="{{ route('faculty.grades.import.create') }}" class="btn btn-outline-light"><i class="fas fa-file-import me-2"></i>Run Another Import</a>
          </div>
        </div>

        @if(session('success'))
          <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
        @endif

        <div class="faculty-management-card p-4">
          @if(!empty($summary))
            <div class="row g-3">
              <div class="col-md-3">
                <div class="stat-box">
                  <div class="stat-title">Processed</div>
                  <div class="stat-value">{{ $summary['processed'] ?? 0 }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-box">
                  <div class="stat-title">Created</div>
                  <div class="stat-value">{{ $summary['created'] ?? 0 }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-box">
                  <div class="stat-title">Updated</div>
                  <div class="stat-value">{{ $summary['updated'] ?? 0 }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-box">
                  <div class="stat-title">Errors</div>
                  <div class="stat-value">{{ isset($summary['errors']) ? count($summary['errors']) : 0 }}</div>
                </div>
              </div>
            </div>

            @if(!empty($summary['errors']))
              <div class="mt-4">
                <h6 class="text-white-75">Error Details</h6>
                <div class="bg-dark rounded p-3 border border-light-subtle">
                  <ul class="mb-0 small">
                    @foreach($summary['errors'] as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            @endif

            <div class="mt-4 d-flex gap-2">
              <a href="{{ route('faculty.grades.index') }}" class="btn btn-green"><i class="fas fa-list me-2"></i>View Grades</a>
              <a href="{{ route('faculty.grades.import.create') }}" class="btn btn-outline-light"><i class="fas fa-file-import me-2"></i>Import Again</a>
            </div>
          @else
            <x-ui.alert type="warning" :dismissible="false">No import summary available. Please run an import.</x-ui.alert>
            <a href="{{ route('faculty.grades.import.create') }}" class="btn btn-green"><i class="fas fa-file-import me-2"></i>Go to Import</a>
          @endif
        </div>
      </main>
    </div>
  </div>
</div>
@endsection
