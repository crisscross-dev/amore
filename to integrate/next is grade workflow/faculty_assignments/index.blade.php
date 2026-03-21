@extends('layouts.app')

@section('title', 'Faculty Position Assignments - Admin Dashboard - Amore Academy')

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
            @include('partials.admin-sidebar')

            <main class="col-lg-9 col-md-8">
                <div class="faculty-management-actions mb-3">
                    <form method="GET" action="{{ route('admin.faculty-assignments.index') }}" class="faculty-inline-filters">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name or email">
                        <select name="position_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.faculty-positions.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-layer-group me-1"></i>Manage Positions Catalog
                    </a>
                    <a href="{{ route('admin.accounts.manage') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-users me-1"></i>Back to Manage Accounts
                    </a>
                </div>

                @if(session('success'))
                <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
                @endif

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2 text-success"></i>
                            Faculty Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $facultyMembers->total() }} faculty</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Assigned</th>
                                    <th>Assigned By</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyMembers as $faculty)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-success">{{ $faculty->first_name }} {{ $faculty->last_name }}</div>
                                        <small class="text-muted">{{ $faculty->custom_id }}</small>
                                    </td>
                                    <td>{{ $faculty->email }}</td>
                                    <td>
                                        @if($faculty->facultyPosition)
                                        <span class="badge bg-success">{{ $faculty->facultyPosition->name }}</span>
                                        @else
                                        <span class="badge bg-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $faculty->department ?? '—' }}</td>
                                    <td>{{ $faculty->position_assigned_date ? $faculty->position_assigned_date->format('M d, Y') : '—' }}</td>
                                    <td>{{ optional($faculty->positionAssignee)->first_name ? optional($faculty->positionAssignee)->first_name . ' ' . optional($faculty->positionAssignee)->last_name : '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.faculty-assignments.edit', $faculty) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-pen"></i>
                                            Update
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="faculty-management-empty">
                                            <i class="fas fa-search"></i>
                                            <h5 class="fw-semibold mb-2">No faculty members found</h5>
                                            <p class="mb-0">Adjust your filters or ensure faculty accounts are registered.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pagination-wrapper faculty-assignments-pagination">
                        {{ $facultyMembers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection