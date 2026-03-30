<div class="admissions-card mb-4" id="gradeApprovalLiveSection" data-live-url="{{ route('admin.grade-approvals.live-section') }}" data-sheet="{{ $activeSheetFilter }}">
    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <h5 class="mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-clipboard-check"></i>
            Grade Sheet
        </h5>
        <div class="d-flex align-items-center gap-2 sheet-filter-tabs">
            <a href="{{ route('admin.grade-approvals.index', ['sheet' => 'pending']) }}"
                class="btn btn-sm grade-filter-btn {{ $activeSheetFilter === 'pending' ? 'active' : '' }}">
                Pending
            </a>
            <a href="{{ route('admin.grade-approvals.index', ['sheet' => 'approved']) }}"
                class="btn btn-sm grade-filter-btn {{ $activeSheetFilter === 'approved' ? 'active' : '' }}">
                Approved
            </a>
        </div>
    </div>
    <div class="card-body grade-approvals-body">

        @if($sheetGroups->isEmpty())
        <div class="faculty-management-empty">
            <i class="fas fa-hourglass-half"></i>
            <h5 class="fw-semibold mb-2">No {{ $activeSheetFilter }} sheets</h5>
            <p class="mb-0">
                {{ $activeSheetFilter === 'approved' ? 'Approved grade sheets will appear here.' : 'Grade sheets from faculty will appear here for review and approval.' }}
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle grade-approvals-table">
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Quarter</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sheetGroups as $sheet)
                    <tr class="grade-approvals-row" data-url="{{ route('admin.grade-approvals.show', $sheet['representative']) }}" title="Double-click to open">
                        <td>
                            <div class="fw-semibold text-success">{{ $sheet['grade_level'] }} - {{ $sheet['section_name'] }}</div>
                            <small class="text-muted">{{ $sheet['student_count'] }} student{{ $sheet['student_count'] == 1 ? '' : 's' }}</small>
                        </td>
                        <td>{{ $sheet['subject_name'] }}</td>
                        <td>{{ $sheet['teacher_name'] }}</td>
                        <td>{{ $sheet['term'] }}</td>
                        <td>{{ $sheet['submitted_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
