@extends('layouts.master')

@section('title')
    Roles
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Roles
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Role List</h4>
                        @can('create roles')
                            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Add New Role
                            </a>
                        @endcan
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <th>Permissions Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit roles')
                                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-info">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete roles')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this role?')) {
                this.submit();
            }
        });
    </script>
@endsection
