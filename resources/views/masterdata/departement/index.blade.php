@extends('layouts.master')

@section('title')
    Departemen List
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Master Data
        @endslot
        @slot('title')
            Departemen List
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="search-departemen" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                @can('create departemen')
                                    <button type="button"
                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"
                                        data-bs-toggle="modal" data-bs-target="#modal-departement" id="btn-add-new">
                                        <i class="mdi mdi-plus me-1"></i> Add New Departemen
                                    </button>
                                @endcan
                            </div>
                        </div><!-- end col-->
                    </div>

                    <div id="table-data">
                        @include('masterdata.departement.table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-departement" tabindex="-1" role="dialog" aria-labelledby="modalDepartementLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDepartementLabel">Add New Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-departement">
                        @csrf
                        <input type="hidden" id="departement-uuid" name="uuid">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="Enter Name">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="code_departement" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code_departement" name="code_departement"
                                required placeholder="Enter Code">
                            <div class="invalid-feedback" id="error-code_departement"></div>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                placeholder="Enter Location">
                            <div class="invalid-feedback" id="error-location"></div>
                        </div>
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter Description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            // Pagination
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page);
            });

            function fetchData(page) {
                var search = $('#search-departemen').val() || '';
                $.ajax({
                    url: "/master-data/departemen?page=" + page + "&search=" + encodeURIComponent(search),
                    success: function(data) {
                        $('#table-data').html(data);
                    }
                });
            }

            var searchTimer;
            $('#search-departemen').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchData(1);
                }, 300);
            });

            // Add New - Reset Form
            $('#btn-add-new').click(function() {
                $('#form-departement')[0].reset();
                $('#departement-uuid').val('');
                $('#modalDepartementLabel').text('Add New Departemen');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
            });

            // Edit
            $(document).on('click', '.edit-btn', function() {
                var uuid = $(this).data('uuid');
                $.get("/master-data/departemen/" + uuid, function(data) {
                    $('#modalDepartementLabel').text('Edit Departemen');
                    $('#departement-uuid').val(data.uuid);
                    $('#name').val(data.name);
                    $('#code_departement').val(data.code_departement);
                    $('#location').val(data.location);
                    $('#is_active').val(data.is_active ? 1 : 0);
                    $('#description').val(data.description);
                    $('#modal-departement').modal('show');
                });
            });

            // Save (Create / Update)
            $('#btn-save').click(function() {
                var uuid = $('#departement-uuid').val();
                var url = uuid ? "/master-data/departemen/" + uuid : "/master-data/departemen";
                var method = uuid ? "PUT" : "POST";

                // Add _method field for PUT request
                var formData = $('#form-departement').serializeArray();
                if (uuid) {
                    formData.push({
                        name: "_method",
                        value: "PUT"
                    });
                }

                $.ajax({
                    url: url,
                    type: "POST", // Always POST, emulate PUT with _method
                    data: formData,
                    success: function(response) {
                        $('#modal-departement').modal('hide');
                        $('#form-departement')[0].reset();
                        // Refresh table
                        fetchData(1);
                        // Show success message
                        toastr.success(response.success, 'Berhasil');
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');

                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            toastr.error('Terjadi kesalahan. Silakan coba lagi.', 'Error');
                        }
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this departemen?')) {
                    var uuid = $(this).data('uuid');
                    $.ajax({
                        url: "/master-data/departemen/" + uuid,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            fetchData(1);
                            toastr.success(response.success, 'Berhasil');
                        },
                        error: function(response) {
                            toastr.error('Gagal menghapus data.', 'Error');
                        }
                    });
                }
            });
        });
    </script>
    <script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };
    </script>
@endsection
