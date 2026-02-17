@extends('layouts.master')

@section('title')
    {{ isset($event) ? 'Edit Event' : 'Buat Event Baru' }}
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 5px 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .section-title {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #556ee6;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .food-item td {
            vertical-align: middle !important;
        }

        .food-item .avatar-md {
            width: 4rem;
            height: 4rem;
            object-fit: cover;
            border-radius: 8px;
        }

        .peserta-table td {
            vertical-align: middle !important;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #74788d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Event
        @endslot
        @slot('title')
            {{ isset($event) ? 'Edit Event' : 'Buat Event Baru' }}
        @endslot
    @endcomponent

    <form id="form-event" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="event-uuid" value="{{ $event->uuid ?? '' }}">

        {{-- Action Buttons (Top) --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('event.index') }}" class="btn btn-sm btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <button type="button" class="btn btn-sm btn-primary" id="btn-save-event">
                <i class="bx bx-save me-1"></i> {{ isset($event) ? 'Perbarui Event' : 'Simpan Event' }}
            </button>
        </div>

        {{-- Section 1: Informasi Event --}}
        <div class="card">
            <div class="card-body">
                <h5 class="section-title"><i class="bx bx-calendar-event me-2"></i>Informasi Event</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="event-name" class="form-label">Nama Event <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="event-name" name="name"
                                value="{{ $event->name ?? '' }}" required placeholder="Masukkan nama event">
                        </div>
                        <div class="mb-3">
                            <label for="event-departemen" class="form-label">Departemen <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="event-departemen" name="id_departemen" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ isset($event) && $event->id_departemen == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event-start" class="form-label">Tanggal Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="event-start" name="start_date"
                                        value="{{ isset($event) ? $event->start_date->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event-end" class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="event-end" name="end_date"
                                        value="{{ isset($event) && $event->end_date ? $event->end_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="event-location" class="form-label">Lokasi</label>
                            <input type="text" class="form-control" id="event-location" name="location"
                                value="{{ $event->location ?? '' }}" placeholder="Masukkan lokasi event">
                        </div>
                        <div class="mb-3">
                            <label for="event-description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="event-description" name="description" rows="3"
                                placeholder="Deskripsi event...">{{ $event->description ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="event-image" class="form-label">Gambar (opsional)</label>
                            <input type="file" class="form-control" id="event-image" name="image" accept="image/*">
                            @if (isset($event) && $event->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image"
                                        style="max-height:80px; border-radius:8px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Daftar Konsumsi / Makanan (Cart Style) --}}
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="section-title mb-0"><i class="bx bx-food-menu me-2"></i>Daftar Konsumsi</h5>
                        </div>

                        {{-- Add food row --}}
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-7">
                                <label class="form-label">Tambah Makanan</label>
                                <select class="form-select" id="select-add-food">
                                    <option value="">Cari & pilih makanan...</option>
                                    @foreach ($foods as $food)
                                        <option value="{{ $food->id }}" data-name="{{ $food->name }}"
                                            data-price="{{ $food->price }}"
                                            data-image="{{ $food->image ? asset('storage/' . $food->image) : '' }}">
                                            {{ $food->name }} - Rp {{ number_format($food->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" id="input-add-food-qty" value="1"
                                    min="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-primary w-100" id="btn-add-food">
                                    <i class="bx bx-plus"></i> Tambah
                                </button>
                            </div>
                        </div>

                        {{-- Food cart table --}}
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-nowrap" id="food-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:80px;">Gambar</th>
                                        <th>Makanan</th>
                                        <th>Harga</th>
                                        <th style="width:130px;">Qty</th>
                                        <th>Subtotal</th>
                                        <th style="width:50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="food-cart-body">
                                    {{-- filled by JS --}}
                                </tbody>
                            </table>
                        </div>

                        <div id="food-empty-state" class="empty-state" style="display:none;">
                            <i class="bx bx-cart"></i>
                            <p>Belum ada makanan ditambahkan.<br>Gunakan form di atas untuk menambahkan.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bx bx-receipt me-2"></i>Ringkasan Konsumsi</h5>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>Jumlah Item :</td>
                                        <td class="text-end"><span id="summary-items">0</span> item</td>
                                    </tr>
                                    <tr>
                                        <td>Total Qty :</td>
                                        <td class="text-end"><span id="summary-qty">0</span> pcs</td>
                                    </tr>
                                    <tr>
                                        <th>Grand Total :</th>
                                        <th class="text-end text-primary"><span id="summary-grand-total">Rp 0</span></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Daftar Peserta (Table with Dept) --}}
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="section-title mb-0"><i class="bx bx-group me-2"></i>Daftar Peserta</h5>
                </div>

                {{-- Add peserta --}}
                <div class="row mb-3 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label">Tambah Peserta</label>
                        <select class="form-select" id="select-add-peserta" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-departemen="{{ $user->detail && $user->detail->departemen ? $user->detail->departemen->name : '-' }}"
                                    data-position="{{ $user->detail ? $user->detail->position : '-' }}">
                                    {{ $user->name }} —
                                    {{ $user->detail && $user->detail->departemen ? $user->detail->departemen->name : '' }}
                                    ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-primary w-100" id="btn-add-peserta">
                            <i class="bx bx-user-plus"></i> Tambah Peserta
                        </button>
                    </div>
                </div>

                {{-- Peserta table --}}
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="peserta-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Nama Peserta</th>
                                <th>Email</th>
                                <th>Departemen</th>
                                <th>Jabatan</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="peserta-table-body">
                            {{-- filled by JS --}}
                        </tbody>
                    </table>
                </div>

                <div id="peserta-empty-state" class="empty-state" style="display:none;">
                    <i class="bx bx-user-x"></i>
                    <p>Belum ada peserta ditambahkan.<br>Gunakan form di atas untuk menambahkan.</p>
                </div>
            </div>
        </div>

    </form>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };

        // ============ DATA STORES ============
        var foodCart = []; // { id, name, price, image, qty }
        var pesertaList = []; // { user_id, name, email, departemen, position }

        function formatRupiah(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        }

        // ============ FOOD CART ============
        function renderFoodCart() {
            var $body = $('#food-cart-body');
            $body.empty();

            if (foodCart.length === 0) {
                $('#food-table').hide();
                $('#food-empty-state').show();
            } else {
                $('#food-table').show();
                $('#food-empty-state').hide();
            }

            var grandTotal = 0,
                totalQty = 0;
            foodCart.forEach(function(item, idx) {
                var sub = item.price * item.qty;
                grandTotal += sub;
                totalQty += item.qty;
                var imgHtml = item.image ?
                    '<img src="' + item.image + '" class="avatar-md" alt="food">' :
                    '<div class="avatar-md bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded"><i class="bx bx-food-menu font-size-24"></i></div>';

                $body.append(`
                    <tr class="food-item" data-idx="${idx}">
                        <td>${imgHtml}</td>
                        <td>
                            <h5 class="font-size-14 text-truncate mb-1">${item.name}</h5>
                            <p class="mb-0 text-muted">Stok tersedia</p>
                            <input type="hidden" name="foods[${idx}][id_food]" value="${item.id}">
                        </td>
                        <td>${formatRupiah(item.price)}</td>
                        <td>
                            <div style="width:110px;">
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-food-minus" data-idx="${idx}">−</button>
                                    <input type="number" class="form-control text-center food-qty-input" name="foods[${idx}][qty]" value="${item.qty}" min="1" data-idx="${idx}" style="max-width:50px;">
                                    <button type="button" class="btn btn-outline-secondary btn-food-plus" data-idx="${idx}">+</button>
                                </div>
                            </div>
                        </td>
                        <td><strong>${formatRupiah(sub)}</strong></td>
                        <td>
                            <a href="javascript:void(0);" class="action-icon text-danger btn-remove-food" data-idx="${idx}">
                                <i class="mdi mdi-trash-can font-size-18"></i>
                            </a>
                        </td>
                    </tr>
                `);
            });

            $('#summary-items').text(foodCart.length);
            $('#summary-qty').text(totalQty);
            $('#summary-grand-total').text(formatRupiah(grandTotal));

            // Disable already-added foods in select
            updateFoodSelectOptions();
        }

        function updateFoodSelectOptions() {
            var addedIds = foodCart.map(function(f) {
                return String(f.id);
            });
            $('#select-add-food option').each(function() {
                var val = $(this).val();
                if (val && addedIds.includes(val)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
            // Refresh Select2
            $('#select-add-food').select2({
                placeholder: 'Cari & pilih makanan...',
                allowClear: true,
                width: '100%'
            });
        }

        // ============ PESERTA TABLE ============
        function renderPesertaTable() {
            var $body = $('#peserta-table-body');
            $body.empty();

            if (pesertaList.length === 0) {
                $('#peserta-table').hide();
                $('#peserta-empty-state').show();
            } else {
                $('#peserta-table').show();
                $('#peserta-empty-state').hide();
            }

            pesertaList.forEach(function(p, idx) {
                $body.append(`
                    <tr class="peserta-item" data-idx="${idx}">
                        <td>${idx + 1}</td>
                        <td>
                            <strong>${p.name}</strong>
                            <input type="hidden" name="pesertas[${idx}][user_id]" value="${p.user_id}">
                        </td>
                        <td><small class="text-muted">${p.email}</small></td>
                        <td><span class="badge badge-soft-info font-size-11">${p.departemen}</span></td>
                        <td>${p.position}</td>
                        <td>
                            <a href="javascript:void(0);" class="action-icon text-danger btn-remove-peserta" data-idx="${idx}">
                                <i class="mdi mdi-trash-can font-size-18"></i>
                            </a>
                        </td>
                    </tr>
                `);
            });

            // Disable already-added users in select
            updatePesertaSelectOptions();
        }

        function updatePesertaSelectOptions() {
            var addedIds = pesertaList.map(function(p) {
                return String(p.user_id);
            });
            $('#select-add-peserta option').each(function() {
                var val = $(this).val();
                if (val && addedIds.includes(val)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
            // Refresh Select2
            $('#select-add-peserta').select2({
                placeholder: 'Cari & pilih peserta...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        }

        // ============ INIT ============
        $(document).ready(function() {
            // Init Select2
            $('#select-add-food').select2({
                placeholder: 'Cari & pilih makanan...',
                allowClear: true,
                width: '100%'
            });
            $('#select-add-peserta').select2({
                placeholder: 'Cari & pilih peserta...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });

            // Pre-fill existing data (edit mode)
            @if (isset($consumtions) && count($consumtions) > 0)
                @foreach ($consumtions as $c)
                    foodCart.push({
                        id: {{ $c->id_food }},
                        name: @json($c->food_name),
                        price: {{ $c->price }},
                        image: @json($c->food && $c->food->image ? asset('storage/' . $c->food->image) : ''),
                        qty: {{ $c->qty }}
                    });
                @endforeach
            @endif

            @if (isset($pesertas) && count($pesertas) > 0)
                @foreach ($pesertas as $p)
                    @php
                        $pUser = \App\Models\User::with('detail.departemen')->find($p->user_id);
                    @endphp
                    @if ($pUser)
                        pesertaList.push({
                            user_id: {{ $pUser->id }},
                            name: @json($pUser->name),
                            email: @json($pUser->email),
                            departemen: @json($pUser->detail && $pUser->detail->departemen ? $pUser->detail->departemen->name : '-'),
                            position: @json($pUser->detail ? $pUser->detail->position ?? '-' : '-')
                        });
                    @endif
                @endforeach
            @endif

            renderFoodCart();
            renderPesertaTable();

            // ---- ADD FOOD ----
            $('#btn-add-food').click(function() {
                var $sel = $('#select-add-food');
                var foodId = $sel.val();
                if (!foodId) {
                    toastr.warning('Pilih makanan terlebih dahulu.', 'Peringatan');
                    return;
                }

                // Check duplicate
                if (foodCart.some(f => String(f.id) === String(foodId))) {
                    toastr.warning('Makanan ini sudah ada dalam daftar.', 'Duplikat');
                    return;
                }

                var opt = $sel.find(':selected');
                var qty = parseInt($('#input-add-food-qty').val()) || 1;

                foodCart.push({
                    id: parseInt(foodId),
                    name: opt.data('name'),
                    price: parseFloat(opt.data('price')),
                    image: opt.data('image') || '',
                    qty: qty
                });

                $sel.val('').trigger('change');
                $('#input-add-food-qty').val(1);
                renderFoodCart();
                toastr.success(opt.data('name') + ' ditambahkan.', 'Berhasil');
            });

            // ---- FOOD QTY CHANGE ----
            $(document).on('click', '.btn-food-minus', function() {
                var idx = $(this).data('idx');
                if (foodCart[idx].qty > 1) {
                    foodCart[idx].qty--;
                    renderFoodCart();
                }
            });
            $(document).on('click', '.btn-food-plus', function() {
                var idx = $(this).data('idx');
                foodCart[idx].qty++;
                renderFoodCart();
            });
            $(document).on('change', '.food-qty-input', function() {
                var idx = $(this).data('idx');
                var val = parseInt($(this).val()) || 1;
                if (val < 1) val = 1;
                foodCart[idx].qty = val;
                renderFoodCart();
            });

            // ---- REMOVE FOOD ----
            $(document).on('click', '.btn-remove-food', function() {
                var idx = $(this).data('idx');
                foodCart.splice(idx, 1);
                renderFoodCart();
            });

            // ---- ADD PESERTA (multiple) ----
            $('#btn-add-peserta').click(function() {
                var $sel = $('#select-add-peserta');
                var selectedIds = $sel.val(); // array of selected IDs
                if (!selectedIds || selectedIds.length === 0) {
                    toastr.warning('Pilih peserta terlebih dahulu.', 'Peringatan');
                    return;
                }

                var addedCount = 0;
                selectedIds.forEach(function(userId) {
                    // Skip duplicates
                    if (pesertaList.some(p => String(p.user_id) === String(userId))) return;

                    var opt = $sel.find('option[value="' + userId + '"]');
                    pesertaList.push({
                        user_id: parseInt(userId),
                        name: opt.data('name'),
                        email: opt.data('email'),
                        departemen: opt.data('departemen') || '-',
                        position: opt.data('position') || '-'
                    });
                    addedCount++;
                });

                $sel.val([]).trigger('change');
                renderPesertaTable();
                if (addedCount > 0) {
                    toastr.success(addedCount + ' peserta ditambahkan.', 'Berhasil');
                } else {
                    toastr.warning('Semua peserta yang dipilih sudah ada dalam daftar.', 'Duplikat');
                }
            });

            // ---- REMOVE PESERTA ----
            $(document).on('click', '.btn-remove-peserta', function() {
                var idx = $(this).data('idx');
                pesertaList.splice(idx, 1);
                renderPesertaTable();
            });

            // ============ SAVE ============
            $('#btn-save-event').click(function() {
                if (foodCart.length === 0) {
                    toastr.error('Minimal harus ada 1 item makanan.', 'Validasi Gagal');
                    return;
                }
                if (pesertaList.length === 0) {
                    toastr.error('Minimal harus ada 1 peserta.', 'Validasi Gagal');
                    return;
                }

                var uuid = $('#event-uuid').val();
                var url = uuid ? "/event/" + uuid : "/event";
                var formData = new FormData($('#form-event')[0]);
                if (uuid) formData.append('_method', 'PUT');

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.success, 'Berhasil');
                        setTimeout(function() {
                            window.location.href = "{{ route('event.index') }}";
                        }, 1000);
                    },
                    error: function(response) {
                        $btn.prop('disabled', false).html(
                            '<i class="bx bx-save me-1"></i> {{ isset($event) ? 'Perbarui Event' : 'Simpan Event' }}'
                        );
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors) {
                                var firstError = Object.values(errors)[0][0];
                                toastr.error(firstError, 'Validasi Gagal');
                            } else if (response.responseJSON.error) {
                                toastr.error(response.responseJSON.error, 'Validasi Gagal');
                            }
                        } else if (response.status === 403) {
                            toastr.error(response.responseJSON.error, 'Akses Ditolak');
                        } else {
                            toastr.error('Terjadi kesalahan.', 'Error');
                        }
                    }
                });
            });
        });
    </script>
@endsection
