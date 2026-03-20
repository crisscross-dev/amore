<form method="GET" action="{{ route('admin.accounts.manage') }}" class="row g-2 align-items-end mb-3">
    <div class="col-md-4">
        <label class="form-label">Grade Level</label>
        <select name="student_grade_level" id="studentGradeFilter" class="form-select">
            <option value="">All Grades</option>
            @foreach($sectionsByGrade->keys() as $grade)
                <option value="{{ $grade }}" {{ ($gradeLevel ?? '') === (string) $grade ? 'selected' : '' }}>
                    Grade {{ $grade }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Section</label>
        <select name="student_section_id" id="studentSectionFilter" class="form-select" data-selected="{{ $sectionId ?? '' }}">
            <option value="">All Sections</option>
            @php
                $selectedGrade = (string) ($gradeLevel ?? '');
                $sections = $selectedGrade !== '' && isset($sectionsByGrade[$selectedGrade]) ? $sectionsByGrade[$selectedGrade] : collect();
            @endphp
            @foreach($sections as $section)
                <option value="{{ $section->id }}" {{ (string) ($sectionId ?? '') === (string) $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-green w-100">
            <i class="fas fa-filter me-2"></i>Filter
        </button>
        <a href="{{ route('admin.accounts.manage') }}" class="btn btn-outline-secondary w-100">
            Reset
        </a>
    </div>
</form>

@if($students->count() > 0)
    <div class="table-responsive mt-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th><i class="fas fa-id-badge me-1"></i>Student ID</th>
                    <th><i class="fas fa-user me-1"></i>Name</th>
                    <th><i class="fas fa-envelope me-1"></i>Email</th>
                    <th><i class="fas fa-graduation-cap me-1"></i>Grade Level</th>
                    <th><i class="fas fa-layer-group me-1"></i>Section</th>
                    <th><i class="fas fa-hashtag me-1"></i>LRN</th>
                    <th><i class="fas fa-circle me-1"></i>Status</th>
                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><strong>{{ $student->custom_id ?? $student->id }}</strong></td>
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
                                <span class="badge bg-info">Grade {{ $student->grade_level }}</span>
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.students.assign-section', $student->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                <select name="section_id" class="form-select form-select-sm section-select" data-grade="{{ $student->grade_level }}" style="min-width: 160px;">
                                    <option value="">-- None --</option>
                                    @php
                                        $grade = (string)($student->grade_level ?? '');
                                        $sections = isset($sectionsByGrade[$grade]) ? $sectionsByGrade[$grade] : collect();
                                    @endphp
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ $student->section_id == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Assign">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
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
                                <a href="{{ route('admin.accounts.show', $student->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.accounts.edit', $student->id) }}" class="btn btn-sm btn-warning" title="Edit Account">
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
        {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h5>No Student Accounts Found</h5>
        <p>There are currently no student accounts in the system.</p>
    </div>
@endif