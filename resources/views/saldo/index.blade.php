@extends('layouts.master')

@section('title')
    Saldo Departemen
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Saldo
        @endslot
        @slot('title')
            Saldo Departemen
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
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="table-data">
                        @include('saldo.table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Transaksi -->
    <div class="modal fade" id="modal-transaksi" tabindex="-1" role="dialog" aria-labelledby="modalTransaksiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTransaksiLabel">Transaksi Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-transaksi">
                        @csrf
                        <input type="hidden" id="transaksi-uuid" name="departemen_uuid">
                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" id="transaksi-departemen" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="transaksi-status" class="form-label">Tipe Transaksi</label>
                            <select class="form-select" id="transaksi-status" name="status">
                                <option value="masuk">Saldo Masuk</option>
                                <option value="keluar">Saldo Keluar</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah-display" class="form-label">Jumlah (Rp)</label>
                            <input type="text" class="form-control" id="jumlah-display" required
                                placeholder="Contoh: 100.000.000" autocomplete="off">
                            <input type="hidden" id="jumlah" name="jumlah">
                            <div class="invalid-feedback" id="error-jumlah"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="transaksi-description" name="description" rows="3"
                                placeholder="Masukkan keterangan transaksi"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-save-transaksi">Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Log -->
    <div class="modal fade" id="modal-log" tabindex="-1" role="dialog" aria-labelledby="modalLogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogLabel">Log Transaksi Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="fw-bold">Departemen: </span>
                        <span id="log-departemen-name"></span>
                        <span class="ms-3 fw-bold">Saldo Saat Ini: </span>
                        <span id="log-saldo-current" class="fw-bold text-success"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="log-table-body">
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                $.ajax({
                    url: "/saldo?page=" + page,
                    success: function(data) {
                        $('#table-data').html(data);
                    }
                });
            }

            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            // Input Mask - format angka dengan titik (100.000.000)
            $(document).on('input', '#jumlah-display', function() {
                var value = $(this).val().replace(/\D/g, ''); // hapus non-digit
                var formatted = new Intl.NumberFormat('id-ID').format(value);
                $(this).val(value ? formatted : '');
                $('#jumlah').val(value); // simpan angka asli di hidden input
            });

            // Open Transaksi Modal
            $(document).on('click', '.btn-transaksi', function() {
                var uuid = $(this).data('uuid');
                var departemen = $(this).data('departemen');
                $('#form-transaksi')[0].reset();
                $('#jumlah-display').val('');
                $('#jumlah').val('');
                $('#transaksi-uuid').val(uuid);
                $('#transaksi-departemen').val(departemen);
                $('#modalTransaksiLabel').text('Transaksi Saldo - ' + departemen);
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#modal-transaksi').modal('show');
            });

            // Save Transaksi
            $('#btn-save-transaksi').click(function() {
                var formData = $('#form-transaksi').serializeArray();

                $.ajax({
                    url: "/saldo",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#modal-transaksi').modal('hide');
                        $('#form-transaksi')[0].reset();
                        fetchData(1);
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

            // Open Log Modal
            $(document).on('click', '.btn-log', function() {
                var uuid = $(this).data('uuid');
                var departemen = $(this).data('departemen');
                $('#modalLogLabel').text('Log Transaksi - ' + departemen);
                $('#log-departemen-name').text(departemen);
                $('#log-table-body').html(
                    '<tr><td colspan="6" class="text-center">Loading...</td></tr>');

                $.get("/saldo/" + uuid, function(data) {
                    $('#log-saldo-current').text(formatRupiah(data.saldo));

                    var rows = '';
                    if (data.logs.length === 0) {
                        rows =
                            '<tr><td colspan="6" class="text-center">Belum ada transaksi.</td></tr>';
                    } else {
                        $.each(data.logs, function(index, log) {
                            var statusBadge = log.status === 'masuk' ?
                                '<span class="badge badge-soft-success font-size-11">Masuk</span>' :
                                '<span class="badge badge-soft-danger font-size-11">Keluar</span>';

                            var tanggal = new Date(log.created_at);
                            var formattedDate = tanggal.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            rows += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + formattedDate + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td class="fw-bold ' + (log.status === 'masuk' ?
                                    'text-success' : 'text-danger') + '">' +
                                (log.status === 'masuk' ? '+' : '-') + ' ' +
                                formatRupiah(log.saldo) + '</td>' +
                                '<td>' + (log.description || '-') + '</td>' +
                                '<td>' + (log.created_name || '-') + '</td>' +
                                '</tr>';
                        });
                    }
                    $('#log-table-body').html(rows);
                });

                $('#modal-log').modal('show');
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
