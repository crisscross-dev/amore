@extends('layouts.app')

@section('title', 'Enrollment Approvals - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])
@push('styles')
<style>
    #assignSectionSubjectsBox,
    #assignSectionSubjectsList li {
        color: #212529;
    }

    #assignSectionSubjectsEmpty {
        color: #6c757d !important;
    }

    .stats-counters-small .card {
        margin-bottom: 0.75rem !important;
    }

    .stats-counters-small .card-body {
        padding: 0.7rem 0.9rem;
    }

    .stats-counters-small .h3 {
        font-size: 1.3rem;
        margin-bottom: 0;
        line-height: 1.1;
    }

    .stats-counters-small .small {
        font-size: 0.75rem;
    }

    .stats-counters-small .fs-1 {
        font-size: 1.5rem !important;
    }

    .search-input-compact {
        height: calc(2.25rem + 2px);
        padding: 0;
    }

    .assign-modal-scroll {
        max-height: 62vh;
        overflow-y: auto;
    }
</style>
@endpush

<div class="dashboard-container enrollments-live-page"
    data-live-url="{{ route('admin.enrollments.live-signature') }}"
    data-live-signature="{{ $enrollmentsLiveSignature ?? '' }}">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->

            <!-- Main Content -->
            <main class="col-12">
                <!-- Mobile Profile (Hidden on Desktop) -->
                <div class="d-md-none mobile-profile mb-4">
                    <div class="text-center">
                        <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                            alt="Profile Picture"
                            class="rounded-circle mb-3 border border-3 border-white"
                            width="80"
                            height="80">

                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            Administrator | {{ Auth::user()->custom_id ?? 'ADMIN-0001' }}
                        </p>

                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('dashboard.admin') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-tachometer-alt d-block mb-1"></i>
                                    <small>Dashboard</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-calendar-alt d-block mb-1"></i>
                                    <small>Calendar</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('announcements.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-bullhorn d-block mb-1"></i>
                                    <small>Announce</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.enrollments.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-user-plus d-block mb-1"></i>
                                    <small>Enrollments</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.subjects.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-book d-block mb-1"></i>
                                    <small>Subjects</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-check d-block mb-1"></i>
                                    <small>Admissions</small>
                                </a>
                            </div>
                        </div>

                        <hr class="bg-white opacity-25 my-3">

                        <button
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>

                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        <i class="fas fa-user-plus me-2"></i>
                        Enrollment Approval
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('calendar.create') }}" class="btn btn-success btn-m">
                            <i class="fas fa-plus me-2"></i>Add Activity
                        </a>
                    </div> -->
                </div>

                <!-- Flash Messages -->


                <!-- Statistics Cards -->
                <div class="row mb-4 stats-counters-small">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Pending</div>
                                        <div class="h3">{{ $stats['pending'] }}</div>
                                    </div>
                                    <i class="bi bi-clock-history fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Approved</div>
                                        <div class="h3">{{ $stats['approved'] }}</div>
                                    </div>
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Rejected</div>
                                        <div class="h3">{{ $stats['rejected'] }}</div>
                                    </div>
                                    <i class="bi bi-x-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Total</div>
                                        <div class="h3">{{ $stats['total'] }}</div>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="admissions-card mb-4">
                    <div class="card-body p-2">
                        <form id="enrollmentFiltersForm" action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3 m-0">
                            <div class="col-md-3">
                                <select name="status" class="form-select filter-auto-submit">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="grade_level" class="form-select filter-auto-submit">
                                    <option value="">All Grade Levels</option>
                                    @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="school_year_id" class="form-select filter-auto-submit">
                                    @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->year_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control search-input-compact" placeholder="Student name" value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="admissions-card mt-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-check me-2"></i>Approved Admissions Ready for Enrollment</span>
                        <span class="badge bg-light text-primary">{{ $admissionsReady->total() }}</span>
                    </div>
                    <div class="card-body">
                        @if($admissionsReady->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Grade Level</th>
                                        <th>School Level</th>
                                        <th>Approved At</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admissionsReady as $admission)
                                    <tr>
                                        <td>
                                            <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>
                                            <br><small class="text-muted">{{ $admission->email ?: 'No email provided' }}</small>
                                        </td>
                                        <td>{{ $admission->grade_level ?: 'N/A' }}</td>
                                        <td>{{ strtoupper($admission->school_level) }}</td>
                                        <td>{{ optional($admission->approved_at)->format('M d, Y h:i A') ?: 'N/A' }}</td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary open-assign-modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#assignSectionModal"
                                                data-admission-id="{{ $admission->id }}"
                                                data-enroll-url="{{ route('admin.enrollments.enroll-admission', $admission) }}"
                                                data-name="{{ trim($admission->first_name . ' ' . $admission->middle_name . ' ' . $admission->last_name) }}"
                                                data-email="{{ $admission->email ?: 'N/A' }}"
                                                data-contact="{{ $admission->phone ?: 'N/A' }}"
                                                data-lrn="{{ $admission->lrn ?: 'N/A' }}"
                                                data-grade-level="{{ $admission->grade_level ?: '' }}"
                                                data-school-level="{{ strtoupper($admission->school_level ?: 'N/A') }}">
                                                <i class="fas fa-user-plus me-1"></i>Assign Section
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $admissionsReady->links() }}
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No approved admissions are waiting for enrollment processing.</p>
                        </div>
                        @endif
                    </div>
                </div>

                @php
                $sectionMetaMap = $sections->mapWithKeys(function ($section) {
                $subjectNames = $section->subjectTeachers
                ->pluck('subject.name')
                ->filter()
                ->unique()
                ->values();

                return [(string) $section->id => [
                'id' => $section->id,
                'name' => $section->name,
                'grade_level' => $section->grade_level,
                'subjects' => $subjectNames,
                ]];
                });
                $sectionMetaMapEncoded = base64_encode($sectionMetaMap->toJson());
                @endphp

                <div class="modal fade" id="assignSectionModal" tabindex="-1" aria-labelledby="assignSectionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="assignSectionModalLabel"><i class="fas fa-user-plus me-2"></i>Assign Section</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="assignSectionForm" method="POST" action="">
                                @csrf
                                <input type="hidden" name="admission_id" id="assign_admission_id" value="{{ old('admission_id') }}">
                                <div class="modal-body assign-modal-scroll">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Student Name</small>
                                            <strong id="assign_student_name">-</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Email</small>
                                            <strong id="assign_student_email">-</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Contact</small>
                                            <strong id="assign_student_contact">-</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">LRN</small>
                                            <strong id="assign_student_lrn">-</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Grade / Level</small>
                                            <strong id="assign_student_grade">-</strong>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 mb-3">
                                        <small>
                                            <strong>School Year:</strong>
                                            {{ $selectedSchoolYear?->year_name ?? 'No school year found. Please create one first.' }}
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="assign_section_id" class="form-label">Assign Section <span class="text-danger">*</span></label>
                                        <select name="section_id" id="assign_section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                            <option value="">Select section</option>
                                        </select>
                                        @error('section_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted" id="assign_section_help">Only sections matching the applicant grade level are shown.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subjects in Selected Section</label>
                                        <div id="assignSectionSubjectsBox" class="border rounded p-2 bg-light-subtle" style="min-height: 72px;">
                                            <p class="text-muted mb-0" id="assignSectionSubjectsEmpty">Select a section to view assigned subjects.</p>
                                            <ul class="mb-0 ps-3 d-none" id="assignSectionSubjectsList"></ul>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label for="assign_admin_remarks" class="form-label">Remarks (Optional)</label>
                                        <textarea name="admin_remarks" id="assign_admin_remarks" rows="3" class="form-control @error('admin_remarks') is-invalid @enderror">{{ old('admin_remarks') }}</textarea>
                                        @error('admin_remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" {{ $selectedSchoolYear ? '' : 'disabled' }}>
                                        <i class="fas fa-user-plus me-1"></i>Assign Section
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="assign-section-map" data-map="{{ $sectionMetaMapEncoded }}" class="d-none"></div>
                <div
                    id="assign-modal-state"
                    data-old-section="{{ old('section_id', '') }}"
                    data-old-admission="{{ old('admission_id', '') }}"
                    data-has-errors="{{ $errors->has('section_id') || $errors->has('admin_remarks') ? '1' : '0' }}"
                    class="d-none"></div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const liveContainer = document.querySelector('.enrollments-live-page');
        const liveUrl = liveContainer ? (liveContainer.getAttribute('data-live-url') || '') : '';
        let liveSignature = liveContainer ? (liveContainer.getAttribute('data-live-signature') || '') : '';
        let liveRequestInFlight = false;
        let livePollTimer = null;

        const enrollmentFiltersForm = document.getElementById('enrollmentFiltersForm');
        const autoSubmitFilters = document.querySelectorAll('.filter-auto-submit');
        const modalElement = document.getElementById('assignSectionModal');
        const formElement = document.getElementById('assignSectionForm');
        const sectionSelect = document.getElementById('assign_section_id');
        const admissionIdInput = document.getElementById('assign_admission_id');
        const sectionHelp = document.getElementById('assign_section_help');
        const subjectsList = document.getElementById('assignSectionSubjectsList');
        const subjectsEmpty = document.getElementById('assignSectionSubjectsEmpty');
        const sectionMapElement = document.getElementById('assign-section-map');
        const modalStateElement = document.getElementById('assign-modal-state');
        const assignButtons = document.querySelectorAll('.open-assign-modal');
        const oldSectionId = modalStateElement ? modalStateElement.getAttribute('data-old-section') : '';

        let sectionMetaMap = {};
        let activeGrade = '';

        const anyModalOpen = () => !!document.querySelector('.modal.show');

        const buildLiveUrl = () => {
            const url = new URL(liveUrl, window.location.origin);
            const currentParams = new URLSearchParams(window.location.search);

            currentParams.forEach((value, key) => {
                url.searchParams.set(key, value);
            });

            return url.toString();
        };

        const checkLiveSignature = async () => {
            if (!liveUrl || liveRequestInFlight) {
                return;
            }

            liveRequestInFlight = true;

            try {
                const response = await fetch(buildLiveUrl(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const nextSignature = payload && payload.signature ? payload.signature : '';

                if (!nextSignature) {
                    return;
                }

                if (!liveSignature) {
                    liveSignature = nextSignature;
                    if (liveContainer) {
                        liveContainer.setAttribute('data-live-signature', nextSignature);
                    }
                    return;
                }

                if (nextSignature !== liveSignature) {
                    if (anyModalOpen()) {
                        return;
                    }

                    window.location.reload();
                }
            } catch (error) {
                console.debug('Enrollments live polling skipped:', error);
            } finally {
                liveRequestInFlight = false;
            }
        };

        if (liveUrl) {
            livePollTimer = window.setInterval(() => {
                if (!document.hidden) {
                    checkLiveSignature();
                }
            }, 10000);

            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkLiveSignature();
                }
            });

            window.addEventListener('beforeunload', function() {
                if (livePollTimer) {
                    clearInterval(livePollTimer);
                    livePollTimer = null;
                }
            }, {
                once: true
            });
        }

        autoSubmitFilters.forEach((filterElement) => {
            filterElement.addEventListener('change', function() {
                if (enrollmentFiltersForm) {
                    enrollmentFiltersForm.submit();
                }
            });
        });

        try {
            const encodedMap = sectionMapElement ? sectionMapElement.getAttribute('data-map') : '';
            sectionMetaMap = encodedMap ? JSON.parse(atob(encodedMap)) : {};
        } catch (error) {
            sectionMetaMap = {};
        }

        const normalizeGradeLevel = (gradeValue) => {
            if (!gradeValue) {
                return '';
            }

            const matches = String(gradeValue).match(/(7|8|9|10|11|12)/);
            return matches ? matches[1] : String(gradeValue).trim();
        };

        const renderSubjects = () => {
            const sectionId = sectionSelect.value;
            const selectedSection = sectionMetaMap[sectionId];
            const subjects = selectedSection && Array.isArray(selectedSection.subjects) ? selectedSection.subjects : [];

            subjectsList.innerHTML = '';

            if (!sectionId) {
                subjectsEmpty.textContent = 'Select a section to view assigned subjects.';
                subjectsEmpty.classList.remove('d-none');
                subjectsList.classList.add('d-none');
                return;
            }

            if (!subjects.length) {
                subjectsEmpty.textContent = 'No subjects are assigned to this section yet.';
                subjectsEmpty.classList.remove('d-none');
                subjectsList.classList.add('d-none');
                return;
            }

            subjects.forEach((subjectName) => {
                const listItem = document.createElement('li');
                listItem.textContent = subjectName;
                subjectsList.appendChild(listItem);
            });

            subjectsEmpty.classList.add('d-none');
            subjectsList.classList.remove('d-none');
        };

        const populateSections = (gradeValue, selectedSectionId = '') => {
            sectionSelect.innerHTML = '<option value="">Select section</option>';

            const normalizedGrade = normalizeGradeLevel(gradeValue);
            const filteredSections = Object.values(sectionMetaMap).filter((section) => {
                const sectionGrade = normalizeGradeLevel(section.grade_level);
                return !normalizedGrade || sectionGrade === normalizedGrade;
            });

            filteredSections.forEach((section) => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = `${section.name} (${section.grade_level})`;
                if (selectedSectionId && String(selectedSectionId) === String(section.id)) {
                    option.selected = true;
                }
                sectionSelect.appendChild(option);
            });

            if (!filteredSections.length) {
                sectionHelp.textContent = 'No available sections match this applicant grade level.';
            } else {
                sectionHelp.textContent = 'Only sections matching the applicant grade level are shown.';
            }

            renderSubjects();
        };

        const setModalStudentData = (buttonElement) => {
            const admissionId = buttonElement.getAttribute('data-admission-id') || '';
            const enrollUrl = buttonElement.getAttribute('data-enroll-url') || '';
            const name = buttonElement.getAttribute('data-name') || 'N/A';
            const email = buttonElement.getAttribute('data-email') || 'N/A';
            const contact = buttonElement.getAttribute('data-contact') || 'N/A';
            const lrn = buttonElement.getAttribute('data-lrn') || 'N/A';
            const gradeLevel = buttonElement.getAttribute('data-grade-level') || '';
            const schoolLevel = buttonElement.getAttribute('data-school-level') || 'N/A';

            activeGrade = gradeLevel;
            formElement.action = enrollUrl;
            admissionIdInput.value = admissionId;

            document.getElementById('assign_student_name').textContent = name;
            document.getElementById('assign_student_email').textContent = email;
            document.getElementById('assign_student_contact').textContent = contact;
            document.getElementById('assign_student_lrn').textContent = lrn;
            document.getElementById('assign_student_grade').textContent = `${gradeLevel || 'N/A'} / ${schoolLevel}`;

            populateSections(activeGrade, oldSectionId || '');
        };

        assignButtons.forEach((buttonElement) => {
            buttonElement.addEventListener('click', function() {
                setModalStudentData(buttonElement);
            });
        });

        sectionSelect.addEventListener('change', renderSubjects);

        const hasValidationErrors = modalStateElement && modalStateElement.getAttribute('data-has-errors') === '1';
        const oldAdmissionId = modalStateElement ? modalStateElement.getAttribute('data-old-admission') : '';

        if (hasValidationErrors) {
            const matchingButton = oldAdmissionId ?
                document.querySelector(`.open-assign-modal[data-admission-id="${oldAdmissionId}"]`) :
                null;

            if (matchingButton) {
                setModalStudentData(matchingButton);
                const modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            }
        }
    });
</script>
@endsection
