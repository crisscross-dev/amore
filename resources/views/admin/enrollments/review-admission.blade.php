@extends('layouts.app')

@section('title', 'Enrollment Processing - Admin Dashboard - Amore Academy')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])
@push('styles')
<style>
    #sectionSubjectsBox,
    #sectionSubjectsBox li {
        color: #212529;
    }

    #sectionSubjectsEmpty {
        color: #6c757d !important;
    }
</style>
@endpush
<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-user-plus me-2"></i>Enrollment Processing</h4>
                        <p class="mb-0 opacity-90">Assign section for the approved student account</p>
                    </div>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Enrollment List</a>
                </div>



                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Approved Applicant</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong class="text-success">Name:</strong> <span class="text-dark">{{ $admission->first_name }} {{ $admission->middle_name }} {{ $admission->last_name }}</span></p>
                                        <p><strong class="text-success">Email:</strong> <span class="text-dark">{{ $admission->email ?: 'N/A' }}</span></p>
                                        <p><strong class="text-success">Contact:</strong> <span class="text-dark">{{ $admission->phone ?: 'N/A' }}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong class="text-success">LRN:</strong> <span class="text-dark">{{ $admission->lrn }}</span></p>
                                        <p><strong class="text-success">Grade Level:</strong> <span class="badge bg-info">{{ $admission->grade_level ?: 'N/A' }}</span></p>
                                        <p><strong class="text-success">School Level:</strong> <span class="text-dark">{{ strtoupper($admission->school_level) }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Finalize Enrollment</h6>
                            </div>
                            <div class="card-body">
                                @php
                                $sectionSubjectMap = $sections->mapWithKeys(function ($section) {
                                $subjectNames = $section->subjectTeachers
                                ->pluck('subject.name')
                                ->filter()
                                ->unique()
                                ->values();

                                return [(string) $section->id => $subjectNames];
                                });
                                $sectionSubjectMapEncoded = base64_encode($sectionSubjectMap->toJson());
                                @endphp
                                <form id="enrollAdmissionForm" method="POST" action="{{ route('admin.enrollments.enroll-admission', $admission) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="section_id" class="form-label">Assign Section <span class="text-danger">*</span></label>
                                        <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                            <option value="">Select section</option>
                                            @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }} ({{ $section->grade_level }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('section_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Only sections for this grade level are listed.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subjects in Selected Section</label>
                                        <div id="sectionSubjectsBox" class="border rounded p-2 bg-light-subtle" style="min-height: 72px;">
                                            <p class="text-muted mb-0" id="sectionSubjectsEmpty">Select a section to view assigned subjects.</p>
                                            <ul class="mb-0 ps-3 d-none" id="sectionSubjectsList"></ul>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="admin_remarks" class="form-label">Remarks (Optional)</label>
                                        <textarea name="admin_remarks" id="admin_remarks" rows="3" class="form-control @error('admin_remarks') is-invalid @enderror">{{ old('admin_remarks') }}</textarea>
                                        @error('admin_remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if($selectedSchoolYear)
                                    <div class="alert alert-info py-2">
                                        <small>
                                            <strong>School Year:</strong> {{ $selectedSchoolYear->year_name }}
                                        </small>
                                    </div>
                                    @else
                                    <div class="alert alert-danger py-2">
                                        <small>No school year found. Please create one first.</small>
                                    </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary w-100" {{ $selectedSchoolYear ? '' : 'disabled' }}>
                                        <i class="fas fa-user-plus me-1"></i>Assign Section
                                    </button>
                                </form>

                                <div id="section-subject-map" data-map="{{ $sectionSubjectMapEncoded }}" class="d-none"></div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const sectionSelect = document.getElementById('section_id');
                                        const subjectsList = document.getElementById('sectionSubjectsList');
                                        const emptyText = document.getElementById('sectionSubjectsEmpty');
                                        const sectionSubjectMapElement = document.getElementById('section-subject-map');
                                        let sectionSubjectMap = {};

                                        try {
                                            const encodedMap = sectionSubjectMapElement ? sectionSubjectMapElement.getAttribute('data-map') : '';
                                            sectionSubjectMap = encodedMap ? JSON.parse(atob(encodedMap)) : {};
                                        } catch (error) {
                                            sectionSubjectMap = {};
                                        }

                                        const renderSubjects = () => {
                                            const sectionId = sectionSelect ? sectionSelect.value : '';
                                            const subjects = sectionSubjectMap[sectionId] || [];

                                            subjectsList.innerHTML = '';

                                            if (!sectionId) {
                                                emptyText.textContent = 'Select a section to view assigned subjects.';
                                                emptyText.classList.remove('d-none');
                                                subjectsList.classList.add('d-none');
                                                return;
                                            }

                                            if (!subjects.length) {
                                                emptyText.textContent = 'No subjects are assigned to this section yet.';
                                                emptyText.classList.remove('d-none');
                                                subjectsList.classList.add('d-none');
                                                return;
                                            }

                                            subjects.forEach((subjectName) => {
                                                const li = document.createElement('li');
                                                li.textContent = subjectName;
                                                subjectsList.appendChild(li);
                                            });

                                            emptyText.classList.add('d-none');
                                            subjectsList.classList.remove('d-none');
                                        };

                                        if (sectionSelect) {
                                            sectionSelect.addEventListener('change', renderSubjects);
                                        }

                                        renderSubjects();
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
