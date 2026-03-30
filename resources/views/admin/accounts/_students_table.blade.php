<form method="GET" action="{{ route('admin.accounts.manage') }}" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="tab" value="students">
    <div class="col-md-4">
        <select name="student_grade_level" id="studentGradeFilter" class="form-select" onchange="this.form.submit()">
            <option value="">All Grades</option>
            @foreach($studentGradeLevels as $gradeRow)
            @php
            $gradeValue = (string) $gradeRow->grade_level;
            $gradeLabel = preg_match('/^Grade\s+/i', $gradeValue) ? $gradeValue : ('Grade ' . $gradeValue);
            @endphp
            <option value="{{ $gradeValue }}" {{ ($gradeLevel ?? '') === $gradeValue ? 'selected' : '' }}>
                {{ $gradeLabel }} ({{ $gradeRow->total }})
            </option>
            @endforeach
            @if(($gradeLevel ?? '') !== '' && !collect($studentGradeLevels)->pluck('grade_level')->contains((string) $gradeLevel))
            <option value="{{ $gradeLevel }}" selected>{{ preg_match('/^Grade\s+/i', (string) $gradeLevel) ? $gradeLevel : ('Grade ' . $gradeLevel) }}</option>
            @endif
        </select>
    </div>
</form>

@if($students->count() > 0)
<div class="table-responsive mt-3">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th><i class="fas fa-user me-1"></i>Name</th>
                <th><i class="fas fa-envelope me-1"></i>Email</th>
                <th><i class="fas fa-graduation-cap me-1"></i>Grade Level</th>
                <th><i class="fas fa-hashtag me-1"></i>LRN</th>
                <th><i class="fas fa-circle me-1"></i>Status</th>
                <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr class="js-account-row" data-view-modal="#studentViewModal{{ $student->id }}" title="Double-click to view details">
                <td class="text-center"><strong>{{ ($students->firstItem() ?? 1) + $loop->index }}</strong></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            @if($student->profile_picture)
                            <img src="{{ asset('uploads/profile_picture/' . $student->profile_picture) }}"
                                alt="Profile"
                                class="rounded-circle"
                                width="32"
                                height="32"
                                style="object-fit: cover;">
                            @else
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px; font-size: 14px;">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                        </div>
                    </div>
                </td>
                <td>{{ $student->email }}</td>
                <td>
                    @if($student->grade_level)
                    <span class="badge bg-info">{{ $student->grade_level }}</span>
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                </td>
                <td>
                    @if($student->lrn)
                    <span class="text-monospace">{{ $student->lrn }}</span>
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                </td>
                <td>
                    @if($student->status === 'active')
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Active
                    </span>
                    @elseif($student->status === 'for_approval')
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-clock me-1"></i>For Approval
                    </span>
                    @else
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>Inactive
                    </span>
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-warning" title="Edit Account" data-bs-toggle="modal" data-bs-target="#studentEditModal{{ $student->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-wrapper mt-4">
    {{ $students->appends(array_merge(request()->query(), ['tab' => 'students']))->links('pagination::bootstrap-5') }}
</div>

@foreach($students as $student)
<div class="modal fade" id="studentViewModal{{ $student->id }}" tabindex="-1" aria-labelledby="studentViewModalLabel{{ $student->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentViewModalLabel{{ $student->id }}">
                    <i class="fas fa-user me-2"></i>Student Account Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-5 text-muted">Student ID</div>
                    <div class="col-7 fw-semibold">{{ $student->custom_id ?? $student->id }}</div>

                    <div class="col-5 text-muted">Name</div>
                    <div class="col-7 fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>

                    <div class="col-5 text-muted">Email</div>
                    <div class="col-7">{{ $student->email }}</div>

                    <div class="col-5 text-muted">Grade Level</div>
                    <div class="col-7">{{ $student->grade_level ? 'Grade ' . $student->grade_level : 'Not set' }}</div>

                    <div class="col-5 text-muted">LRN</div>
                    <div class="col-7">{{ $student->lrn ?: 'Not set' }}</div>

                    <div class="col-5 text-muted">Status</div>
                    <div class="col-7">
                        @if($student->status === 'active')
                        <span class="badge bg-success">Active</span>
                        @elseif($student->status === 'for_approval')
                        <span class="badge bg-warning text-dark">For Approval</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="studentEditModal{{ $student->id }}" tabindex="-1" aria-labelledby="studentEditModalLabel{{ $student->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentEditModalLabel{{ $student->id }}">
                    <i class="fas fa-pen-to-square me-2"></i>Edit Student Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.accounts.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="students">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $student->first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ $student->middle_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $student->last_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $student->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ $student->contact_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grade Level</label>
                            <input type="text" name="grade_level" class="form-control" value="{{ $student->grade_level }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" placeholder="Leave blank to keep current password">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Confirm new password">
                        </div>
                        <input type="hidden" name="department" value="{{ $student->department }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@else
<div class="empty-state">
    <i class="fas fa-users"></i>
    <h5>No Student Accounts Found</h5>
    <p>There are currently no student accounts in the system.</p>
</div>
@endif