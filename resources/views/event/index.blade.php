@extends('layouts.master')

@section('title')
    Seluruh Event
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}">
    <style>
        .status-filter .btn {
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Event
        @endslot
        @slot('title')
            Seluruh Event
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-2">Daftar Event</h4>
                        @can('create events')
                            <a href="{{ route('event.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Buat Event Baru
                            </a>
                        @endcan
                    </div>

                    {{-- Search & Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search-event" placeholder="Cari event..."
                                    value="">
                                <button class="btn btn-outline-secondary" type="button" id="btn-search">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="status-filter d-flex flex-wrap gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary filter-status active"
                                    data-status="">Semua</button>
                                @foreach (\App\Models\event\Event::STATUS_LABELS as $key => $label)
                                    <button class="btn btn-sm btn-outline-secondary filter-status"
                                        data-status="{{ $key }}">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div id="event-table-wrapper">
                        @include('event.table', ['events' => $events])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="modalDetailEvent" tabindex="-1" aria-labelledby="modalDetailEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailEventLabel">Detail Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detail-event-body">
                    {{-- filled dynamically --}}
                </div>
                <div class="modal-footer" id="detail-event-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Action Modal --}}
    <div class="modal fade" id="modalStatusAction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusActionTitle">Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="statusActionMessage"></p>
                    <div class="mb-3" id="status-description-wrapper">
                        <label for="status-description" class="form-label">Catatan / Alasan</label>
                        <textarea class="form-control" id="status-description" rows="3" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-confirm-status">Konfirmasi</button>
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

        var currentStatus = '';

        function formatTanggalEvent(value) {
            if (!value) return '-';
            var dt = new Date(value);
            if (isNaN(dt.getTime())) return '-';

            return dt.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function loadTable() {
            $.ajax({
                url: "{{ route('event.index') }}",
                data: {
                    search: $('#search-event').val(),
                    status: currentStatus
                },
                success: function(html) {
                    $('#event-table-wrapper').html(html);
                }
            });
        }

        $(document).ready(function() {
            // Search
            $('#btn-search').click(function() {
                loadTable();
            });
            $('#search-event').on('keypress', function(e) {
                if (e.which == 13) loadTable();
            });

            // Status filter
            $(document).on('click', '.filter-status', function() {
                $('.filter-status').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                loadTable();
            });

            // Pagination
            $(document).on('click', '#event-table-wrapper .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (url) {
                    $.get(url + '&search=' + $('#search-event').val() + '&status=' + currentStatus,
                        function(html) {
                            $('#event-table-wrapper').html(html);
                        });
                }
            });

            // Delete Event
            $(document).on('click', '.btn-delete-event', function() {
                var uuid = $(this).data('uuid');
                var name = $(this).data('name');
                if (confirm('Apakah Anda yakin ingin menghapus event "' + name + '"?')) {
                    $.ajax({
                        url: "/event/" + uuid,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(r) {
                            toastr.success(r.success, 'Berhasil');
                            loadTable();
                        },
                        error: function(r) {
                            toastr.error(r.responseJSON?.error || 'Terjadi kesalahan.',
                            'Error');
                        }
                    });
                }
            });

            // Show detail
            $(document).on('click', '.btn-show-event', function() {
                var uuid = $(this).data('uuid');
                $.getJSON("/event/" + uuid, function(data) {
                    var event = data.event;
                    var statusLabels = @json(\App\Models\event\Event::STATUS_LABELS);
                    var statusBadges = @json(\App\Models\event\Event::STATUS_BADGES);
                    var startDate = formatTanggalEvent(event.start_date);
                    var endDate = event.end_date ? formatTanggalEvent(event.end_date) : null;

                    var html = `<div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><th width="40%">Nama Event</th><td>${event.name}</td></tr>
                                <tr><th>Departemen</th><td>${event.name_departemen || '-'}</td></tr>
                                <tr><th>Tanggal</th><td>${startDate}${endDate ? ' s/d ' + endDate : ''}</td></tr>
                                <tr><th>Lokasi</th><td>${event.location || '-'}</td></tr>
                                <tr><th>Status</th><td><span class="badge ${statusBadges[event.status] || 'badge-soft-secondary'} font-size-11">${statusLabels[event.status] || 'Unknown'}</span></td></tr>
                                <tr><th>Dibuat oleh</th><td>${event.name_user_created}</td></tr>
                                ${event.reject_reason ? '<tr><th>Alasan Reject</th><td class="text-danger">' + event.reject_reason + '</td></tr>' : ''}
                            </table>
                        </div>
                        <div class="col-md-6">
                            ${event.image ? '<img src="/storage/' + event.image + '" class="img-fluid rounded" style="max-height:200px;">' : ''}
                            ${event.description ? '<p class="mt-2">' + event.description + '</p>' : ''}
                        </div>
                    </div>
                    <hr>`;

                    // Tabs
                    html += `<ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-konsumsi">Konsumsi (${data.consumtions.length})</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-peserta">Peserta (${data.pesertas.length})</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-timeline">Timeline (${data.statusLogs.length})</a></li>
                    </ul>
                    <div class="tab-content pt-3">`;

                    // Tab: Konsumsi
                    html +=
                        '<div class="tab-pane active" id="tab-konsumsi"><table class="table table-sm"><thead><tr><th>#</th><th>Makanan</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
                    var grandTotal = 0;
                    data.consumtions.forEach(function(c, i) {
                        grandTotal += parseFloat(c.total);
                        html +=
                            `<tr><td>${i+1}</td><td>${c.food_name}</td><td>${c.qty}</td><td>Rp ${new Intl.NumberFormat('id-ID').format(c.price)}</td><td>Rp ${new Intl.NumberFormat('id-ID').format(c.total)}</td></tr>`;
                    });
                    html +=
                        `</tbody><tfoot><tr><th colspan="4" class="text-end">Grand Total</th><th>Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}</th></tr></tfoot></table></div>`;

                    // Tab: Peserta
                    html +=
                        '<div class="tab-pane" id="tab-peserta"><table class="table table-sm"><thead><tr><th>#</th><th>Nama Peserta</th><th>Status</th></tr></thead><tbody>';
                    data.pesertas.forEach(function(p, i) {
                        html +=
                            `<tr><td>${i+1}</td><td>${p.user_name}</td><td>${p.status == 1 ? '<span class="badge badge-soft-success">Hadir</span>' : '<span class="badge badge-soft-danger">Tidak Hadir</span>'}</td></tr>`;
                    });
                    html += '</tbody></table></div>';

                    // Tab: Timeline
                    html +=
                        '<div class="tab-pane" id="tab-timeline"><table class="table table-sm"><thead><tr><th>Waktu</th><th>Dari</th><th>Ke</th><th>Oleh</th><th>Catatan</th></tr></thead><tbody>';
                    data.statusLogs.forEach(function(log) {
                        html += `<tr><td><small>${new Date(log.created_at).toLocaleString('id-ID')}</small></td>
                            <td>${log.status_from ? (statusLabels[log.status_from] || log.status_from) : '-'}</td>
                            <td><strong>${statusLabels[log.status_to] || log.status_to}</strong></td>
                            <td>${log.user_name}</td>
                            <td>${log.description || '-'}</td></tr>`;
                    });
                    html += '</tbody></table></div></div>';

                    $('#detail-event-body').html(html);
                    $('#detail-event-footer').html(buildActionButtons(event, data.allowed_actions || []));
                    $('#modalDetailEvent').modal('show');
                });
            });
            // Build action buttons based on status + allowed actions
            function buildActionButtons(event, allowedActions) {
                var btns = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
                switch (event.status) {
                    case 1:
                        if (!allowedActions.includes('approve') && !allowedActions.includes('reject')) break;
                        btns = '<button class="btn btn-success btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="approve" data-label="Approve VP">Approve VP</button> ' +
                            '<button class="btn btn-danger btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="reject" data-label="Reject">Reject</button> ' + btns;
                        break;
                    case 2:
                        if (!allowedActions.includes('approve')) break;
                        btns = '<button class="btn btn-info btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="approve" data-label="Terima & Proses">Terima & Proses</button> ' + btns;
                        break;
                    case 3:
                        if (!allowedActions.includes('approve') && !allowedActions.includes('reject')) break;
                        btns = '<button class="btn btn-success btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="approve" data-label="Approve VP Umum">Approve VP Umum</button> ' +
                            '<button class="btn btn-danger btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="reject" data-label="Reject">Reject</button> ' + btns;
                        break;
                    case 4:
                        if (!allowedActions.includes('close')) break;
                        btns = '<button class="btn btn-warning btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="close" data-label="Konfirmasi Kirim">Konfirmasi Kirim</button> ' + btns;
                        break;
                    case 6:
                        if (!allowedActions.includes('close')) break;
                        btns = '<button class="btn btn-primary btn-status-action" data-uuid="' + event.uuid +
                            '" data-action="close" data-label="Konfirmasi Terima">Konfirmasi Terima</button> ' +
                            btns;
                        break;
                }
                return btns;
            }

            // Status action
            var statusUuid, statusAction;
            $(document).on('click', '.btn-status-action', function() {
                statusUuid = $(this).data('uuid');
                statusAction = $(this).data('action');
                var label = $(this).data('label');
                $('#statusActionTitle').text('Konfirmasi: ' + label);
                $('#statusActionMessage').text('Apakah Anda yakin ingin melakukan aksi "' + label +
                    '" pada event ini?');
                $('#status-description').val('');
                // Show description field for reject, optional for others
                if (statusAction === 'reject') {
                    $('#status-description-wrapper label').text('Alasan Reject *');
                } else {
                    $('#status-description-wrapper label').text('Catatan (opsional)');
                }
                $('#modalDetailEvent').modal('hide');
                $('#modalStatusAction').modal('show');
            });

            $('#btn-confirm-status').click(function() {
                if (statusAction === 'reject' && !$('#status-description').val().trim()) {
                    toastr.error('Alasan reject wajib diisi.', 'Validasi Gagal');
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true);
                $.ajax({
                    url: "/event/" + statusUuid + "/status",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        action: statusAction,
                        description: $('#status-description').val()
                    },
                    success: function(r) {
                        toastr.success(r.success, 'Berhasil');
                        $('#modalStatusAction').modal('hide');
                        loadTable();
                    },
                    error: function(r) {
                        var message = r.responseJSON?.error || r.responseJSON?.message;
                        if (!message && r.responseJSON?.errors) {
                            var firstKey = Object.keys(r.responseJSON.errors)[0];
                            message = r.responseJSON.errors[firstKey][0];
                        }
                        toastr.error(message || 'Terjadi kesalahan.', 'Error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection

