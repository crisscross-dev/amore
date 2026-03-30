@extends('layouts.app-student')

@section('title', 'Enrollment Details - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

<div class="dashboard-container student-enrollment-show-live-page"
    data-live-url="{{ route('student.enrollment.show.live-signature', $enrollment) }}"
    data-live-signature="{{ $enrollmentShowLiveSignature ?? '' }}">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-file-alt me-2"></i>Enrollment Details</h4>
                        <p class="mb-0 opacity-90">View your enrollment information</p>
                    </div>
                    <a href="{{ route('student.enrollment.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>



                <!-- Enrollment Information Card -->
                <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Enrollment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong class="text-success">School Year:</strong> <span class="text-dark">{{ $enrollment->schoolYear->year_name }}</span></p>
                            <p><strong class="text-success">Current Grade Level:</strong> <span class="text-dark">{{ $enrollment->current_grade_level }}</span></p>
                            <p><strong class="text-success">Enrolling for:</strong> <span class="badge bg-info">{{ $enrollment->enrolling_grade_level }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong class="text-success">Enrollment Date:</strong> <span class="text-dark">{{ $enrollment->enrollment_date->format('M d, Y h:i A') }}</span></p>
                            <p><strong class="text-success">Status:</strong> 
                                @if($enrollment->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($enrollment->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($enrollment->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </p>
                            @if($enrollment->section)
                                <p><strong class="text-success">Assigned Section:</strong> <span class="text-dark">{{ $enrollment->section->name }}</span></p>
                            @endif
                        </div>
                    </div>

                    @if($enrollment->admin_remarks)
                        <div class="alert alert-info">
                            <strong>Admin Remarks:</strong><br>
                            {{ $enrollment->admin_remarks }}
                        </div>
                    @endif

                    @if($enrollment->status === 'approved' && $enrollment->approvedBy)
                        <p class="text-muted small">
                            Approved by {{ $enrollment->approvedBy->first_name }} {{ $enrollment->approvedBy->last_name }} 
                            on {{ $enrollment->approved_at->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
            </div>


                </div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var liveContainer = document.querySelector('.student-enrollment-show-live-page');
        var liveUrl = liveContainer ? (liveContainer.getAttribute('data-live-url') || '') : '';
        var liveSignature = liveContainer ? (liveContainer.getAttribute('data-live-signature') || '') : '';
        var liveRequestInFlight = false;
        var livePollTimer = null;

        function buildLiveUrl() {
            var url = new URL(liveUrl, window.location.origin);
            var currentParams = new URLSearchParams(window.location.search);

            currentParams.forEach(function(value, key) {
                url.searchParams.set(key, value);
            });

            return url.toString();
        }

        function checkLiveSignature() {
            if (!liveUrl || liveRequestInFlight) {
                return;
            }

            liveRequestInFlight = true;

            fetch(buildLiveUrl(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        return null;
                    }

                    return response.json();
                })
                .then(function(payload) {
                    if (!payload || !payload.signature) {
                        return;
                    }

                    var nextSignature = payload.signature;

                    if (!liveSignature) {
                        liveSignature = nextSignature;
                        if (liveContainer) {
                            liveContainer.setAttribute('data-live-signature', nextSignature);
                        }
                        return;
                    }

                    if (nextSignature !== liveSignature) {
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.debug('Student enrollment show live polling skipped:', error);
                })
                .finally(function() {
                    liveRequestInFlight = false;
                });
        }

        if (!liveUrl) {
            return;
        }

        livePollTimer = window.setInterval(function() {
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
    });
</script>
@endsection

