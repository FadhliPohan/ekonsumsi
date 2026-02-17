@extends('layouts.master')

@section('title')
    Edit Role
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Edit Role
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Role: {{ $role->name }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                        <label class="form-check-label fw-bold" for="select-all">
                                            Select All Permissions
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $permission->id }}"
                                                        id="permission-{{ $permission->id }}"
                                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Select all permissions
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Update select-all state when individual checkboxes change
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.permission-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
                const selectAll = document.getElementById('select-all');

                selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
            });
        });

        // Set initial state of select-all checkbox
        window.addEventListener('DOMContentLoaded', function() {
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
            const selectAll = document.getElementById('select-all');

            selectAll.checked = allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0;
        });
    </script>
@endsection
