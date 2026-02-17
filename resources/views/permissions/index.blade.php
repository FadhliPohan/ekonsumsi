@extends('layouts.master')

@section('title')
    Permissions Management
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Permissions
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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Permissions List</h4>
                        @can('create permissions')
                            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Add New Permission
                            </a>
                        @endcan
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission Name</th>
                                <th>Roles Count</th>
                                <th>Users Count</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-success font-size-12">{{ $permission->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $permission->roles->count() }} roles</span>
                                    </td>
                                    <td>{{ $permission->users->count() }} users</td>
                                    <td>{{ $permission->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit permissions')
                                                <a href="{{ route('permissions.edit', $permission) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete permissions')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $permission->id }}"
                                                    action="{{ route('permissions.destroy', $permission) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
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
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();

            // Delete button click handler
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const name = $(this).data('name');

                if (confirm('Are you sure you want to delete permission "' + name +
                        '"?\n\nThis action cannot be undone.')) {
                    $('#delete-form-' + id).submit();
                }
            });
        });
    </script>
@endsection
