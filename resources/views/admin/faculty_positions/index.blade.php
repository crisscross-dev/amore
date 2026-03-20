@extends('layouts.app')

@section('title', 'Faculty Positions - Admin Dashboard - Amore Academy')

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

            <main class="col-12">
                <div class="welcome-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <h4 class="mb-2">
                                <i class="fas fa-user-tie me-2"></i>
                                Faculty Positions
                            </h4>
                            <p class="mb-0 opacity-90">
                                Maintain the roles and hierarchy that guide faculty responsibilities.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Quick actions</span>
                                <a href="{{ route('admin.faculty-positions.create') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    Create New Position
                                </a>
                                <a href="{{ route('admin.faculty-assignments.index') }}">
                                    <i class="fas fa-user-check"></i>
                                    Manage Assignments
                                </a>
                                <a href="{{ route('admin.accounts.manage') }}">
                                    <i class="fas fa-users"></i>
                                    Back to Manage Accounts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
                @endif

                @if($errors->any())
                    <x-ui.alert type="danger" :dismissible="true">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2 text-success"></i>
                            Position Catalog
                        </h5>
                        <a href="{{ route('admin.faculty-positions.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>New Position
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Hierarchy</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($positions as $position)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-success">{{ $position->name }}</div>
                                            <small class="text-muted">{{ $position->description ?: 'No description provided' }}</small>
                                        </td>
                                        <td><span class="badge bg-success bg-opacity-75 text-uppercase">{{ $position->code }}</span></td>
                                        <td class="text-capitalize">{{ $position->category }}</td>
                                        <td>{{ $position->hierarchy_level }}</td>
                                        <td>
                                            @if($position->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.faculty-positions.edit', $position) }}" class="btn btn-outline-success">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('admin.faculty-positions.destroy', $position) }}" method="POST" onsubmit="return confirm('Delete this position?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="faculty-management-empty">
                                                <i class="fas fa-layer-group"></i>
                                                <h5 class="fw-semibold mb-2">No faculty positions defined yet</h5>
                                                <p class="mb-0">Start by adding the roles your faculty can be assigned to.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

