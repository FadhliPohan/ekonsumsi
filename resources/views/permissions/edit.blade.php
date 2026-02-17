@extends('layouts.master')

@section('title')
    Edit Permission
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Edit Permission
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Permission: {{ $permission->name }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $permission->name) }}" required
                                placeholder="e.g., view users, edit media">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle"></i> Use lowercase with spaces. Examples: "view users", "create
                                media", "delete roles"
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bx bx-error-circle"></i> <strong>Warning:</strong>
                            <p class="mb-0 mt-2">
                                Changing the permission name will affect all roles and users that have this permission.
                                Make sure to update your application code if you reference this permission by name.
                            </p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Permission Usage</h5>

                    <div class="mt-3">
                        <h6 class="font-size-13 text-muted">Assigned to Roles:</h6>
                        @if ($permission->roles->count() > 0)
                            <ul class="list-unstyled">
                                @foreach ($permission->roles as $role)
                                    <li class="mb-2">
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted font-size-12">Not assigned to any role</p>
                        @endif

                        <h6 class="font-size-13 text-muted mt-3">Direct User Assignments:</h6>
                        @if ($permission->users->count() > 0)
                            <ul class="list-unstyled">
                                @foreach ($permission->users as $user)
                                    <li class="mb-2">
                                        <i class="bx bx-user"></i> {{ $user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted font-size-12">No direct user assignments</p>
                        @endif
                    </div>

                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bx bx-info-circle"></i>
                            <strong>Total Usage:</strong><br>
                            {{ $permission->roles->count() }} roles,
                            {{ $permission->users->count() }} direct users
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
