@if($pending->count() > 0)
<div class="table-responsive mt-3">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th><i class="fas fa-id-badge me-1"></i>ID</th>
                <th><i class="fas fa-user me-1"></i>Name</th>
                <th><i class="fas fa-envelope me-1"></i>Email</th>
                <th><i class="fas fa-circle me-1"></i>Account Type</th>
                <th><i class="fas fa-graduation-cap me-1"></i>Grade / Dept</th>
                <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pending as $account)
            <tr>
                <td><strong>{{ $account->custom_id ?? $account->id }}</strong></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            @if($account->profile_picture)
                            <img src="{{ asset('uploads/profile_picture/' . $account->profile_picture) }}"
                                alt="Profile"
                                class="rounded-circle"
                                width="32"
                                height="32"
                                style="object-fit: cover;">
                            @else
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px; font-size: 14px;">
                                {{ strtoupper(substr($account->first_name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $account->first_name }} {{ $account->last_name }}</strong>
                        </div>
                    </div>
                </td>
                <td>{{ $account->email }}</td>
                <td>{{ ucfirst($account->account_type) }}</td>
                <td>
                    @if($account->account_type === 'student')
                    @if($account->grade_level)
                    <span class="badge bg-info">Grade {{ $account->grade_level }}</span>
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                    @else
                    @if($account->department)
                    <span class="badge bg-info">{{ $account->department }}</span>
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <button type="button"
                            class="btn btn-sm btn-success approve-btn"
                            title="Approve"
                            data-user-id="{{ $account->id }}"
                            data-user-name="{{ $account->first_name }} {{ $account->last_name }}"
                            data-user-email="{{ $account->email }}"
                            data-account-type="{{ $account->account_type }}"
                            data-grade-level="{{ $account->grade_level ?? '' }}"
                            data-lrn="{{ $account->lrn ?? '' }}"
                            data-department="{{ $account->department ?? '' }}"
                            data-registered="{{ $account->created_at->format('M d, Y') }}">
                            <i class="fas fa-check"></i>
                        </button>
                        <form action="{{ route('admin.accounts.reject', $account->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="pagination-wrapper mt-4">
    {{ $pending->appends(array_merge(request()->query(), ['tab' => 'for-approval']))->links('pagination::bootstrap-5') }}
</div>
@else
<div class="empty-state">
    <i class="fas fa-inbox"></i>
    <h5>No Pending Account Approval</h5>
    <p>There are currently no accounts waiting for approval.</p>
</div>
@endif

<!-- Approve Account Modal -->
<div class="modal" id="approveAccountModal" tabindex="-1" aria-labelledby="approveAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="approveAccountModalLabel">
                    <i class="fas fa-user-check text-success me-2"></i>
                    Approve Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h5 id="modalUserName" class="mb-3">User Name</h5>
                </div>

                <div class="row g-2">
                    <div class="col-4">
                        <strong><i class="fas fa-envelope text-muted me-1"></i>Email:</strong>
                    </div>
                    <div class="col-8" id="modalUserEmail">user@example.com</div>

                    <div class="col-4">
                        <strong><i class="fas fa-user-tag text-muted me-1"></i>Type:</strong>
                    </div>
                    <div class="col-8">
                        <span id="modalAccountType" class="badge bg-primary">Student</span>
                    </div>

                    <!-- Conditional fields for students -->
                    <div class="col-4 student-field" style="display: none;">
                        <strong><i class="fas fa-graduation-cap text-muted me-1"></i>Grade:</strong>
                    </div>
                    <div class="col-8 student-field" id="modalGradeLevel" style="display: none;">Grade 10</div>

                    <div class="col-4 student-field" style="display: none;">
                        <strong><i class="fas fa-id-card text-muted me-1"></i>LRN:</strong>
                    </div>
                    <div class="col-8 student-field" id="modalLRN" style="display: none;">123456789012</div>

                    <!-- Conditional fields for faculty -->
                    <div class="col-4 faculty-field" style="display: none;">
                        <strong><i class="fas fa-building text-muted me-1"></i>Department:</strong>
                    </div>
                    <div class="col-8 faculty-field" id="modalDepartment" style="display: none;">Mathematics</div>

                    <div class="col-4">
                        <strong><i class="fas fa-calendar text-muted me-1"></i>Registered:</strong>
                    </div>
                    <div class="col-8" id="modalRegisteredDate">Oct 22, 2025</div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    <small>By approving this account, the user will be able to access the system.</small>
                </div>

                <!-- Email Send Option -->
                <div class="form-check mt-3 mb-0">
                    <input class="form-check-input" type="checkbox" id="sendEmail" name="send_email" value="1" checked>
                    <label class="form-check-label" for="sendEmail">
                        <i class="fas fa-envelope text-muted me-1"></i>
                        Send approval email to user
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form id="approveAccountForm" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" id="sendEmailInput" name="send_email" value="1">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Approve Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>