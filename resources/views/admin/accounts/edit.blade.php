@extends('layouts.app')

@section('title', 'Edit Account - Admin')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="welcome-card mb-4">
                    <h4 class="mb-0">Edit Account</h4>
                </div>

                <div class="card admissions-card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.accounts.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $user->contact_number) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Grade Level</label>
                                    <input type="text" name="grade_level" class="form-control" value="{{ old('grade_level', $user->grade_level) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                                <a href="{{ route('admin.accounts.show', $user) }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </form>

                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

@endsection

