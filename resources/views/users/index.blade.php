@extends('layouts.master')

@section('title')
    Users
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            User Management
        @endslot
        @slot('title')
            Users
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
                        <h4 class="card-title">User List</h4>
                        @can('create users')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Add New User
                            </a>
                        @endcan
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Departemen</th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->detail->departemen->name ?? '-' }}</td>
                                    <td>{{ $user->detail->position ?? '-' }}</td>
                                    <td>{{ $user->detail->phone ?? '-' }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            @php
                                                $badgeClass = match ($role->name) {
                                                    'Admin' => 'bg-danger',
                                                    'Manager' => 'bg-warning',
                                                    'Karyawan' => 'bg-info',
                                                    default => 'bg-primary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit users')
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete users')
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
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
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };

        $(document).ready(function() {
            $('#datatable').DataTable();

            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                if (confirm('Yakin ingin menghapus user ini?')) {
                    this.submit();
                }
            });
        });
    </script>
@endsection
