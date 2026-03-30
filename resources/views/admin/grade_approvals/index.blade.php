@extends('layouts.app')

@section('title', 'Grade Approvals - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
'resources/css/layouts/dashboard-roles/dashboard-admin.css',
'resources/css/admin/faculty-management.css',
'resources/css/admin/grade-approvals.css',
])

<style>
    .grade-approvals-table thead th {
        white-space: nowrap;
    }

    .grade-approvals-table {
        margin-bottom: 0;
    }

    .grade-approvals-table thead th {
        padding-top: 0.65rem;
    }

    .grade-approvals-row {
        cursor: pointer;
    }

    .grade-approvals-row:hover {
        background-color: rgba(25, 135, 84, 0.06);
    }

    .sheet-filter-tabs .btn {
        min-width: 110px;
    }

    .sheet-filter-tabs .grade-filter-btn {
        color: #fff !important;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.65);
    }

    .sheet-filter-tabs .grade-filter-btn:hover,
    .sheet-filter-tabs .grade-filter-btn:focus {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.8);
    }

    .sheet-filter-tabs .grade-filter-btn.active {
        background: #198754;
        border-color: #198754;
    }

    .grade-approvals-body {
        padding-top: 0.5rem;
    }
</style>

<div class="dashboard-container grade-approvals-page">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Grade Approval
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-m">
                            <i class="fas fa-plus me-2"></i>New Section
                        </a>
                    </div> -->
                </div>

                @include('admin.grade_approvals.partials.sheet-card')
            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var liveSectionEl = document.getElementById('gradeApprovalLiveSection');
        var pollingTimerId = null;
        var isRefreshing = false;

        document.addEventListener('dblclick', function(event) {
            var row = event.target.closest('.grade-approvals-row[data-url]');
            if (!row) {
                return;
            }

            var url = row.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        });

        function refreshGradeApprovalsSection() {
            if (!liveSectionEl || isRefreshing || document.hidden) {
                return;
            }

            var liveUrl = liveSectionEl.getAttribute('data-live-url');
            if (!liveUrl) {
                return;
            }

            var activeSheet = liveSectionEl.getAttribute('data-sheet') || 'pending';
            var requestUrl = new URL(liveUrl, window.location.origin);
            requestUrl.searchParams.set('sheet', activeSheet);

            isRefreshing = true;

            fetch(requestUrl.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Live grade approvals request failed');
                    }

                    return response.json();
                })
                .then(function(payload) {
                    if (!payload || !payload.html) {
                        return;
                    }

                    var tempWrapper = document.createElement('div');
                    tempWrapper.innerHTML = payload.html.trim();
                    var freshSection = tempWrapper.firstElementChild;

                    if (!freshSection) {
                        return;
                    }

                    liveSectionEl.replaceWith(freshSection);
                    liveSectionEl = freshSection;
                })
                .catch(function() {
                    // Ignore intermittent polling failures.
                })
                .finally(function() {
                    isRefreshing = false;
                });
        }

        pollingTimerId = window.setInterval(refreshGradeApprovalsSection, 10000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshGradeApprovalsSection();
            }
        });

        window.addEventListener('beforeunload', function() {
            if (pollingTimerId) {
                window.clearInterval(pollingTimerId);
            }
        });
    });
</script>
@endpush
@endsection