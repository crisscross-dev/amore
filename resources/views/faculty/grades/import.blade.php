@extends('layouts.app')

@section('title', 'Import Grades - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-file-import me-2"></i>Import Grades</h4>
              <p class="mb-0 opacity-90">Upload a CSV file to create or update grade entries. You can submit after import.</p>
            </div>
            <a href="{{ asset('templates/grades_template.csv') }}" class="btn btn-outline-light"><i class="fas fa-download me-2"></i>Download Template</a>
          </div>
        </div>

        @if($errors->any())
          <x-ui.alert type="danger" :dismissible="true">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </x-ui.alert>
        @endif

        @if(session('success'))
          <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
        @endif

        <div class="faculty-management-card p-4">
          <form action="{{ route('faculty.grades.import.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-12">
              <label class="form-label">Upload File (CSV)</label>
              <input type="file" name="import_file" accept=".csv,.txt" class="form-control" required>
              <small class="text-secondary">Columns: lrn, subject_name, term, grade_value, remarks</small>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="submit_after_import" name="submit_after_import">
                <label class="form-check-label text-dark" for="submit_after_import">
                  Submit grades after import (otherwise saved as drafts)
                </label>
              </div>
            </div>
            <div class="col-12 d-flex gap-2">
              <button class="btn btn-green"><i class="fas fa-upload me-2"></i>Start Import</button>
              <a href="{{ route('faculty.grades.index') }}" class="btn btn-outline-light">Cancel</a>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>
</div>
@endsection
