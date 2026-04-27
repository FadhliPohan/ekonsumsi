@extends('layouts.master')

@section('title')
    Daftar Makanan
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
    <style>
        .food-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .food-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
        }

        .food-img-wrapper {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .food-img-wrapper img {
            max-height: 100%;
            max-width: 100%;
            object-fit: cover;
        }

        .badge-stock {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .badge-status {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        #image-preview {
            max-height: 150px;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Master Data
        @endslot
        @slot('title')
            Daftar Makanan
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="row mb-3">
                <div class="col-xl-4 col-sm-6">
                    <div class="mt-2">
                        <h5>Makanan & Minuman</h5>
                    </div>
                </div>
                <div class="col-lg-8 col-sm-6">
                    <div class="mt-4 mt-sm-0 float-sm-end d-sm-flex align-items-center">
                        <div class="search-box me-2">
                            <div class="position-relative">
                                <input type="text" class="form-control border-0" id="search-food"
                                    placeholder="Cari makanan...">
                                <i class="bx bx-search-alt search-icon"></i>
                            </div>
                        </div>
                        <button class="btn btn-success ms-2" id="btn-add-food">
                            <i class="bx bx-plus me-1"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>

            <div id="food-grid">
                @include('food.grid')
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit --}}
    <div class="modal fade" id="modal-food" tabindex="-1" aria-labelledby="modalFoodLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFoodLabel">Tambah Makanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-food" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="food-uuid" name="uuid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="food-name" class="form-label">Nama Makanan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="food-name" name="name" required>
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="food-price" class="form-label">Harga (Rp) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="food-price-display"
                                        placeholder="Contoh: 25.000" autocomplete="off">
                                    <input type="hidden" id="food-price" name="price">
                                    <div class="invalid-feedback" id="error-price"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="food-qty" class="form-label">Stok Tersedia <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="food-qty" name="qty_available"
                                        min="0" required>
                                    <div class="invalid-feedback" id="error-qty_available"></div>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="food-active" name="is_active"
                                        value="1" checked>
                                    <label class="form-check-label" for="food-active">Aktif</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="food-image" class="form-label">Gambar</label>
                                    <input type="file" class="form-control" id="food-image" name="image"
                                        accept="image/*">
                                    <div class="mt-2 text-center" id="image-preview-wrapper" style="display:none;">
                                        <img id="image-preview" src="" alt="Preview">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="food-description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="food-description" name="description" rows="4"
                                        placeholder="Deskripsi makanan..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-save-food">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Stock --}}
    <div class="modal fade" id="modal-stock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStockLabel">Tambah Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-stock">
                        @csrf
                        <input type="hidden" id="stock-uuid">
                        <div class="mb-3">
                            <label class="form-label">Makanan</label>
                            <input type="text" class="form-control" id="stock-food-name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <input type="text" class="form-control" id="stock-current" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="stock-qty" class="form-label">Jumlah Tambah <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock-qty" name="qty" min="1"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="stock-description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="stock-description" name="description" rows="2"
                                placeholder="e.g. Pembelian dari supplier"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-save-stock"><i class="bx bx-plus me-1"></i>
                        Tambah Stok</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Log --}}
    <div class="modal fade" id="modal-log" tabindex="-1" aria-labelledby="modalLogFoodLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogFoodLabel">Log Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Detail</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="log-food-body">
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
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
    <script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };

        function formatRupiah(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        }

        function fetchGrid(page) {
            page = page || 1;
            var search = $('#search-food').val() || '';
            $.ajax({
                url: "/master-data/food?page=" + page + "&search=" + search,
                success: function(data) {
                    $('#food-grid').html(data);
                }
            });
        }

        $(document).ready(function() {
            // Search
            var searchTimer;
            $('#search-food').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchGrid(1);
                }, 400);
            });

            // Pagination
            $(document).on('click', '#food-grid .pagination a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchGrid(page);
            });

            // Price input mask
            $(document).on('input', '#food-price-display', function() {
                var value = $(this).val().replace(/\D/g, '');
                $(this).val(value ? new Intl.NumberFormat('id-ID').format(value) : '');
                $('#food-price').val(value);
            });

            // Image preview
            $('#food-image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').attr('src', e.target.result);
                        $('#image-preview-wrapper').show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // ADD
            $('#btn-add-food').click(function() {
                $('#form-food')[0].reset();
                $('#food-uuid').val('');
                $('#food-price-display').val('');
                $('#food-price').val('');
                $('#food-active').prop('checked', true);
                $('#image-preview-wrapper').hide();
                $('#modalFoodLabel').text('Tambah Makanan');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#modal-food').modal('show');
            });

            // EDIT
            $(document).on('click', '.btn-edit-food', function() {
                var uuid = $(this).data('uuid');
                $('#form-food')[0].reset();
                $.get("/master-data/food/" + uuid, function(data) {
                    var f = data.food;
                    $('#food-uuid').val(f.uuid);
                    $('#food-name').val(f.name);
                    $('#food-price-display').val(new Intl.NumberFormat('id-ID').format(f.price));
                    $('#food-price').val(f.price);
                    $('#food-qty').val(f.qty_available);
                    $('#food-description').val(f.description);
                    $('#food-active').prop('checked', f.is_active == 1);
                    if (f.image) {
                        $('#image-preview').attr('src', '/storage/' + f.image);
                        $('#image-preview-wrapper').show();
                    } else {
                        $('#image-preview-wrapper').hide();
                    }
                    $('#modalFoodLabel').text('Edit Makanan');
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');
                    $('#modal-food').modal('show');
                });
            });

            // SAVE (Create/Update)
            $('#btn-save-food').click(function() {
                var uuid = $('#food-uuid').val();
                var url = uuid ? "/master-data/food/" + uuid : "/master-data/food";
                var formData = new FormData($('#form-food')[0]);
                if (uuid) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modal-food').modal('hide');
                        fetchGrid(1);
                        toastr.success(response.success, 'Berhasil');
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');
                            $.each(errors, function(key, value) {
                                $('#food-' + key).addClass('is-invalid');
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            toastr.error('Terjadi kesalahan.', 'Error');
                        }
                    }
                });
            });

            // DELETE
            $(document).on('click', '.btn-delete-food', function() {
                var uuid = $(this).data('uuid');
                var name = $(this).data('name');
                if (confirm('Yakin ingin menghapus "' + name + '"?')) {
                    $.ajax({
                        url: "/master-data/food/" + uuid,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            fetchGrid(1);
                            toastr.success(response.success, 'Berhasil');
                        },
                        error: function() {
                            toastr.error('Gagal menghapus.', 'Error');
                        }
                    });
                }
            });

            // ADD STOCK
            $(document).on('click', '.btn-stock-food', function() {
                var uuid = $(this).data('uuid');
                var name = $(this).data('name');
                var qty = $(this).data('qty');
                $('#form-stock')[0].reset();
                $('#stock-uuid').val(uuid);
                $('#stock-food-name').val(name);
                $('#stock-current').val(qty);
                $('#modalStockLabel').text('Tambah Stok - ' + name);
                $('#modal-stock').modal('show');
            });

            $('#btn-save-stock').click(function() {
                var uuid = $('#stock-uuid').val();
                $.ajax({
                    url: "/master-data/food/" + uuid + "/stock",
                    type: "POST",
                    data: $('#form-stock').serialize(),
                    success: function(response) {
                        $('#modal-stock').modal('hide');
                        fetchGrid(1);
                        toastr.success(response.success, 'Berhasil');
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            toastr.error(response.responseJSON.errors.qty[0], 'Error');
                        } else {
                            toastr.error('Terjadi kesalahan.', 'Error');
                        }
                    }
                });
            });

            // VIEW LOG
            $(document).on('click', '.btn-log-food', function() {
                var uuid = $(this).data('uuid');
                var name = $(this).data('name');
                $('#modalLogFoodLabel').text('Log Aktivitas - ' + name);
                $('#log-food-body').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                $('#modal-log').modal('show');

                $.get("/master-data/food/" + uuid, function(data) {
                    var rows = '';
                    if (data.logs.length === 0) {
                        rows =
                            '<tr><td colspan="5" class="text-center text-muted">Belum ada log.</td></tr>';
                    } else {
                        $.each(data.logs, function(i, log) {
                            var typeBadge = {
                                'stock_in': '<span class="badge badge-soft-success">Stok Masuk</span>',
                                'stock_out': '<span class="badge badge-soft-danger">Stok Keluar</span>',
                                'price_change': '<span class="badge badge-soft-warning">Ubah Harga</span>',
                            };
                            var dt = new Date(log.created_at);
                            var formatted = dt.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + formatted + '</td>' +
                                '<td>' + (typeBadge[log.type] || log.type) + '</td>' +
                                '<td class="small">' + (log.description || '-') + '</td>' +
                                '<td>' + (log.created_name || '-') + '</td>' +
                                '</tr>';
                        });
                    }
                    $('#log-food-body').html(rows);
                });
            });
        });
    </script>
@endsection
