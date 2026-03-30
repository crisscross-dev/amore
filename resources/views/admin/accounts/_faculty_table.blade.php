@if($faculty->count() > 0)
<div class="table-responsive mt-3">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th><i class="fas fa-id-badge me-1"></i>Faculty ID</th>
                <th><i class="fas fa-user me-1"></i>Name</th>
                <th><i class="fas fa-envelope me-1"></i>Email</th>
                <th><i class="fas fa-building me-1"></i>Department</th>
                <th><i class="fas fa-circle me-1"></i>Status</th>
                <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faculty as $member)
            <tr>
                <td><strong>{{ $member->custom_id ?? $member->id }}</strong></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            @if($member->profile_picture)
                            <img src="{{ asset('uploads/profile_picture/' . $member->profile_picture) }}"
                                alt="Profile"
                                class="rounded-circle"
                                width="32"
                                height="32"
                                style="object-fit: cover;">
                            @else
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px; font-size: 14px;">
                                {{ strtoupper(substr($member->first_name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $member->first_name }} {{ $member->last_name }}</strong>
                        </div>
                    </div>
                </td>
                <td>{{ $member->email }}</td>
                <td>
                    @if($member->department)
                    <span class="badge bg-info">{{ $member->department }}</span>
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                </td>
                <td>
                    @if($member->status === 'active')
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Active
                    </span>
                    @elseif($member->status === 'for_approval')
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
                        <button type="button" class="btn btn-sm btn-warning" title="Edit Account" data-bs-toggle="modal" data-bs-target="#facultyEditModal{{ $member->id }}">
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
    {{ $faculty->appends(array_merge(request()->query(), ['tab' => 'faculty']))->links('pagination::bootstrap-5') }}
</div>

@foreach($faculty as $member)
<div class="modal fade" id="facultyViewModal{{ $member->id }}" tabindex="-1" aria-labelledby="facultyViewModalLabel{{ $member->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facultyViewModalLabel{{ $member->id }}">
                    <i class="fas fa-user-tie me-2"></i>Faculty Account Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-5 text-muted">Faculty ID</div>
                    <div class="col-7 fw-semibold">{{ $member->custom_id ?? $member->id }}</div>

                    <div class="col-5 text-muted">Name</div>
                    <div class="col-7 fw-semibold">{{ $member->first_name }} {{ $member->last_name }}</div>

                    <div class="col-5 text-muted">Email</div>
                    <div class="col-7">{{ $member->email }}</div>

                    <div class="col-5 text-muted">Department</div>
                    <div class="col-7">{{ $member->department ?: 'Not set' }}</div>

                    <div class="col-5 text-muted">Status</div>
                    <div class="col-7">
                        @if($member->status === 'active')
                        <span class="badge bg-success">Active</span>
                        @elseif($member->status === 'for_approval')
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

<div class="modal fade" id="facultyEditModal{{ $member->id }}" tabindex="-1" aria-labelledby="facultyEditModalLabel{{ $member->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facultyEditModalLabel{{ $member->id }}">
                    <i class="fas fa-pen-to-square me-2"></i>Edit Faculty Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.accounts.update', $member->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="faculty">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ $member->first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ $member->middle_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ $member->last_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $member->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="contact_number"
                                class="form-control"
                                value="{{ $member->contact_number }}"
                                inputmode="numeric"
                                maxlength="11"
                                minlength="11"
                                pattern="\d{11}"
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,11)"
                                onkeydown="if(['e','E','+','-'].includes(event.key)){event.preventDefault()}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department" class="form-select" style="height: 50px !important; padding: 0.75rem 2.25rem 0.75rem 1rem; line-height: 1.5;" required>
                                <option value="elementary" {{ strtolower((string) old('department', $member->department)) === 'elementary' ? 'selected' : '' }}>elementary</option>
                                <option value="junior high" {{ strtolower((string) old('department', $member->department)) === 'junior high' ? 'selected' : '' }}>junior high</option>
                                <option value="senior high" {{ strtolower((string) old('department', $member->department)) === 'senior high' ? 'selected' : '' }}>senior high</option>
                            </select>
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
                        <input type="hidden" name="grade_level" value="{{ $member->grade_level }}">
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
    <i class="fas fa-chalkboard-teacher"></i>
    <h5>No Faculty Accounts Found</h5>
    <p>There are currently no faculty accounts in the system.</p>
    <a href="{{ route('admin.faculty-positions.index') }}" class="btn btn-success btn-sm mt-3">
        <i class="fas fa-layer-group me-1"></i>Manage Faculty Positions
    </a>
</div>
@endif