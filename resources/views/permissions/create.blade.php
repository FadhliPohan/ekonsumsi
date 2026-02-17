@extends('layouts.master')

@section('title')
    Create Permission
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Create Permission
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Create New Permission</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required
                                placeholder="e.g., view users, edit media">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle"></i> Use lowercase with spaces. Examples: "view users", "create
                                media", "delete roles"
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-bulb"></i> <strong>Best Practices:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Use descriptive names: <code>view users</code>, <code>edit media</code></li>
                                <li>Follow pattern: <code>[action] [resource]</code></li>
                                <li>Common actions: view, create, edit, delete, manage</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Create Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Permission Examples</h5>
                    <div class="mt-3">
                        <h6 class="font-size-13 text-muted">Media Management:</h6>
                        <ul class="list-unstyled">
                            <li><code>view media</code></li>
                            <li><code>create media</code></li>
                            <li><code>edit media</code></li>
                            <li><code>delete media</code></li>
                        </ul>

                        <h6 class="font-size-13 text-muted mt-3">User Management:</h6>
                        <ul class="list-unstyled">
                            <li><code>view users</code></li>
                            <li><code>create users</code></li>
                            <li><code>edit users</code></li>
                            <li><code>delete users</code></li>
                        </ul>

                        <h6 class="font-size-13 text-muted mt-3">Role Management:</h6>
                        <ul class="list-unstyled">
                            <li><code>view roles</code></li>
                            <li><code>create roles</code></li>
                            <li><code>edit roles</code></li>
                            <li><code>delete roles</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
