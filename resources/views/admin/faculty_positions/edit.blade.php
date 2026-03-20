@extends('layouts.app')

@section('title', 'Edit Faculty Position - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
    'resources/css/layouts/dashboard-roles/dashboard-admin.css',
    'resources/css/admin/faculty-management.css',
])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="welcome-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <h4 class="mb-2">
                                <i class="fas fa-pen me-2"></i>
                                Edit Faculty Position
                            </h4>
                            <p class="mb-0 opacity-90">
                                Update the details for <strong>{{ $position->name }}</strong> and keep your structure current.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Quick actions</span>
                                <a href="{{ route('admin.faculty-assignments.index') }}">
                                    <i class="fas fa-user-check"></i>
                                    Manage Assignments
                                </a>
                                <a href="{{ route('admin.faculty-positions.index') }}">
                                    <i class="fas fa-layer-group"></i>
                                    View Positions List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card">
                    <a href="{{ route('admin.faculty-positions.index') }}" class="back-link mb-3">
                        <i class="fas fa-arrow-left"></i>
                        Back to positions
                    </a>

                    <form action="{{ route('admin.faculty-positions.update', $position) }}" method="POST" class="row g-4">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label">Position Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $position->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Position Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $position->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="administrative" {{ old('category', $position->category) === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                <option value="teaching" {{ old('category', $position->category) === 'teaching' ? 'selected' : '' }}>Teaching</option>
                                <option value="support" {{ old('category', $position->category) === 'support' ? 'selected' : '' }}>Support</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hierarchy Level <span class="text-danger">*</span></label>
                            <input type="number" name="hierarchy_level" class="form-control @error('hierarchy_level') is-invalid @enderror" value="{{ old('hierarchy_level', $position->hierarchy_level) }}" min="1" max="15" required>
                            <small class="text-muted">Lower numbers indicate higher positions.</small>
                            @error('hierarchy_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $position->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Position is active</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.faculty-positions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update Position
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

