<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <div class="d-flex align-items-center gap-2 text-muted">
        <i class="fas fa-user-tie text-success"></i>
        <span>Assign roles to keep faculty responsibilities clear.</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.faculty-assignments.index') }}" class="btn btn-success btn-sm">
            <i class="fas fa-user-check me-1"></i>Manage Assignments
        </a>
        <a href="{{ route('admin.faculty-positions.index') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-layer-group me-1"></i>Faculty Positions
        </a>
    </div>
</div>

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
                                <a href="{{ route('admin.accounts.show', $member->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.accounts.edit', $member->id) }}" class="btn btn-sm btn-warning" title="Edit Account">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper mt-4">
        {{ $faculty->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
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