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
    <div class="container-fluid px-4 faculty-positions-page">
        <div class="row">
            <main class="col-12">

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

                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        Manage Positions Catalog
                    </h5>
                    <div class="d-none d-lg-block">
                        <a href="{{ route('admin.faculty-positions.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i>New Position
                        </a>
                    </div>
                </div>

                <div class="faculty-management-card faculty-management-table">
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
                                <tr class="faculty-row-clickable"
                                    role="button"
                                    tabindex="0"
                                    data-position-id="{{ $position->id }}"
                                    data-position-name="{{ $position->name }}"
                                    data-position-code="{{ $position->code }}"
                                    data-position-description="{{ $position->description }}"
                                    data-position-category="{{ $position->category }}"
                                    data-position-hierarchy="{{ $position->hierarchy_level }}"
                                    data-position-active="{{ $position->is_active ? '1' : '0' }}">
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
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <form action="{{ route('admin.faculty-positions.destroy', $position) }}" method="POST" onclick="event.stopPropagation();" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

@php
$updateRouteTemplate = route('admin.faculty-positions.update', ['position' => '__POSITION__']);
@endphp

<div class="modal fade" id="facultyPositionModal" tabindex="-1" aria-labelledby="facultyPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl faculty-position-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facultyPositionModalLabel">Edit Faculty Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="facultyPositionForm" method="POST" data-route-template="{{ $updateRouteTemplate }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="mb-3">
                        Editing: <strong id="modalPositionName">Position</strong>
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modalPositionNameInput" class="form-label">Position Name</label>
                            <input type="text" id="modalPositionNameInput" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="modalPositionCodeInput" class="form-label">Code</label>
                            <input type="text" id="modalPositionCodeInput" name="code" class="form-control text-uppercase" required>
                        </div>

                        <div class="col-md-6">
                            <label for="modalPositionCategoryInput" class="form-label">Category</label>
                            <select id="modalPositionCategoryInput" name="category" class="form-select" required>
                                <option value="administrative">Administrative</option>
                                <option value="teaching">Teaching</option>
                                <option value="support">Support</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="modalPositionHierarchyInput" class="form-label">Hierarchy Level</label>
                            <input type="number" id="modalPositionHierarchyInput" name="hierarchy_level" class="form-control" min="1" max="15" required>
                        </div>

                        <div class="col-12">
                            <label for="modalPositionDescriptionInput" class="form-label">Description</label>
                            <textarea id="modalPositionDescriptionInput" name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="modalPositionActiveInput" name="is_active" value="1">
                                <label class="form-check-label" for="modalPositionActiveInput">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('facultyPositionModal');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
        const form = document.getElementById('facultyPositionForm');
        const modalPositionName = document.getElementById('modalPositionName');
        const nameInput = document.getElementById('modalPositionNameInput');
        const codeInput = document.getElementById('modalPositionCodeInput');
        const categoryInput = document.getElementById('modalPositionCategoryInput');
        const hierarchyInput = document.getElementById('modalPositionHierarchyInput');
        const descriptionInput = document.getElementById('modalPositionDescriptionInput');
        const activeInput = document.getElementById('modalPositionActiveInput');
        const routeTemplate = form.getAttribute('data-route-template') || '';

        const populateModal = function(row) {
            const positionId = row.getAttribute('data-position-id') || '';
            const positionName = row.getAttribute('data-position-name') || 'Position';
            const positionCode = row.getAttribute('data-position-code') || '';
            const positionDescription = row.getAttribute('data-position-description') || '';
            const positionCategory = row.getAttribute('data-position-category') || 'teaching';
            const positionHierarchy = row.getAttribute('data-position-hierarchy') || '';
            const positionActive = row.getAttribute('data-position-active') === '1';

            form.action = routeTemplate.replace('__POSITION__', positionId);
            modalPositionName.textContent = positionName;
            nameInput.value = positionName;
            codeInput.value = positionCode;
            categoryInput.value = positionCategory;
            hierarchyInput.value = positionHierarchy;
            descriptionInput.value = positionDescription;
            activeInput.checked = positionActive;
        };

        document.querySelectorAll('.faculty-row-clickable').forEach(function(row) {
            row.addEventListener('dblclick', function() {
                populateModal(row);
                modalInstance.show();
            });

            row.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    populateModal(row);
                    modalInstance.show();
                }
            });
        });

        codeInput.addEventListener('input', function() {
            codeInput.value = codeInput.value.toUpperCase();
        });
    });
</script>
@endpush
@endsection