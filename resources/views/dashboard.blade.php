@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('css')
    <style>
        .mini-stat-card {
            transition: transform .2s;
        }

        .mini-stat-card:hover {
            transform: translateY(-2px);
        }

        .nav-tabs-custom .nav-link {
            font-weight: 600;
            padding: 10px 20px;
        }

        .nav-tabs-custom .nav-link.active {
            border-bottom: 2px solid #556ee6;
            color: #556ee6;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 22px;
        }

        .chart-card {
            min-height: 360px;
        }

        .table-dash td,
        .table-dash th {
            padding: 8px 12px;
            font-size: 13px;
        }

        .low-stock-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .6
            }
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            E-Konsumsi Dashboard
        @endslot
    @endcomponent

    {{-- ═══ KPI CARDS (always visible) ═══ --}}
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="card mini-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-primary bg-soft text-primary me-3"><i class="bx bx-calendar-event"></i></div>
                        <div>
                            <p class="text-muted mb-1 font-size-13">Total Event</p>
                            <h4 class="mb-0">{{ number_format($kpi['total_events']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card mini-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-success bg-soft text-success me-3"><i class="bx bx-money"></i></div>
                        <div>
                            <p class="text-muted mb-1 font-size-13">Total Pengeluaran</p>
                            <h4 class="mb-0">Rp {{ number_format($kpi['total_pengeluaran'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card mini-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-info bg-soft text-info me-3"><i class="bx bx-group"></i></div>
                        <div>
                            <p class="text-muted mb-1 font-size-13">Total Peserta</p>
                            <h4 class="mb-0">{{ number_format($kpi['total_peserta']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card mini-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-warning bg-soft text-warning me-3"><i class="bx bx-food-menu"></i></div>
                        <div>
                            <p class="text-muted mb-1 font-size-13">Menu Tersedia</p>
                            <h4 class="mb-0">{{ number_format($kpi['total_foods']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ PENDING ACTIONS ═══ --}}
    @if ($kpi['pending_events'] > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
                    <i class="bx bx-bell bx-tada font-size-20 me-2"></i>
                    <strong>{{ $kpi['pending_events'] }} event</strong>&nbsp;membutuhkan tindakan (menunggu approval /
                    proses).
                    <a href="{{ route('event.index') }}" class="btn btn-sm btn-warning ms-3">Lihat Event</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ TAB NAVIGATION ═══ --}}
    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-event" role="tab">
                <i class="bx bx-calendar-event me-1"></i> Event & Status
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-konsumsi" role="tab">
                <i class="bx bx-food-menu me-1"></i> Konsumsi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-saldo" role="tab">
                <i class="bx bx-wallet me-1"></i> Saldo & Keuangan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-user" role="tab">
                <i class="bx bx-user me-1"></i> User & Peserta
            </a>
        </li>
    </ul>

    <div class="tab-content p-3 bg-white">

        {{-- ═══════════ TAB 1: EVENT & STATUS ═══════════ --}}
        <div class="tab-pane active" id="tab-event" role="tabpanel">
            <div class="row mt-3">
                {{-- Event per Status (Donut) --}}
                <div class="col-xl-4">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Event per Status</h4>
                            <div id="chart-event-status" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Tren Event per Bulan (Line) --}}
                <div class="col-xl-8">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Tren Event per Bulan</h4>
                            <div id="chart-event-trend" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Event per Departemen (Bar) --}}
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Event per Departemen</h4>
                            <div id="chart-event-dept" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Avg Approval + Event Terbaru --}}
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="kpi-icon bg-info bg-soft text-info me-3"><i class="bx bx-timer"></i></div>
                                <div>
                                    <p class="text-muted mb-1">Rata-rata Waktu Approval</p>
                                    <h3 class="mb-0">{{ $avgApprovalHours }} <small
                                            class="text-muted font-size-14">jam</small></h3>
                                </div>
                            </div>
                            <h5 class="font-size-14 mb-3">Event Terbaru</h5>
                            <div data-simplebar style="max-height: 280px;">
                                <table class="table table-dash table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Event</th>
                                            <th>Departemen</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestEvents as $ev)
                                            <tr>
                                                <td class="text-truncate" style="max-width:150px;">{{ $ev->name }}
                                                </td>
                                                <td>{{ $ev->name_departemen }}</td>
                                                <td>
                                                    @php
                                                        $badges = \App\Models\event\Event::STATUS_BADGES;
                                                        $labels = \App\Models\event\Event::STATUS_LABELS;
                                                    @endphp
                                                    <span
                                                        class="badge {{ $badges[$ev->status] ?? 'badge-soft-secondary' }} font-size-10">{{ $labels[$ev->status] ?? '-' }}</span>
                                                </td>
                                                <td><small>{{ $ev->created_at->format('d M') }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ TAB 2: KONSUMSI ═══════════ --}}
        <div class="tab-pane" id="tab-konsumsi" role="tabpanel">
            <div class="row mt-3">
                {{-- Top 10 Makanan --}}
                <div class="col-xl-8">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Top 10 Makanan Paling Sering Dipesan</h4>
                            <div id="chart-top-foods" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Distribusi Harga --}}
                <div class="col-xl-4">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Distribusi Harga Makanan</h4>
                            <div id="chart-price-dist" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Pengeluaran Konsumsi per Bulan (Area) --}}
                <div class="col-xl-8">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Pengeluaran Konsumsi per Bulan</h4>
                            <div id="chart-monthly-spending" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Stok Rendah + Perubahan Harga --}}
                <div class="col-xl-4">
                    {{-- Low Stock --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bx bx-error-circle text-danger me-1"></i> Stok Rendah
                            </h5>
                            @if ($lowStockFoods->isEmpty())
                                <p class="text-muted text-center py-3">Semua stok aman 👍</p>
                            @else
                                <div data-simplebar style="max-height: 150px;">
                                    <table class="table table-dash table-sm mb-0">
                                        <tbody>
                                            @foreach ($lowStockFoods as $ls)
                                                <tr>
                                                    <td>{{ $ls->name }}</td>
                                                    <td class="text-end"><span
                                                            class="badge bg-danger low-stock-badge">{{ $ls->qty_available }}
                                                            pcs</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Price Changes --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bx bx-trending-up text-warning me-1"></i> Perubahan
                                Harga Terbaru</h5>
                            @if ($priceChanges->isEmpty())
                                <p class="text-muted text-center py-3">Belum ada perubahan harga</p>
                            @else
                                <div data-simplebar style="max-height: 150px;">
                                    <table class="table table-dash table-sm mb-0">
                                        <tbody>
                                            @foreach ($priceChanges as $pc)
                                                <tr>
                                                    <td class="text-truncate" style="max-width:100px;">
                                                        {{ $pc->name }}</td>
                                                    <td class="text-end">
                                                        <small class="text-muted"><del>Rp
                                                                {{ number_format($pc->price_before, 0, ',', '.') }}</del></small>
                                                        → <strong>Rp
                                                            {{ number_format($pc->price_after, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ TAB 3: SALDO & KEUANGAN ═══════════ --}}
        <div class="tab-pane" id="tab-saldo" role="tabpanel">
            <div class="row mt-3">
                {{-- Saldo per Departemen --}}
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Saldo per Departemen</h4>
                            <div id="chart-saldo-dept" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Riwayat Mutasi Saldo --}}
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Riwayat Mutasi Saldo per Bulan</h4>
                            <div id="chart-saldo-mutasi" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Anggaran vs Pengeluaran --}}
                <div class="col-xl-8">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Anggaran vs Pengeluaran per Departemen</h4>
                            <div id="chart-budget-vs-spending" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Top Spending Departments --}}
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bx bx-trophy text-warning me-1"></i> Departemen
                                Pengeluaran Tertinggi</h5>
                            <div data-simplebar style="max-height: 300px;">
                                @foreach ($topSpendingDept as $idx => $ts)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs me-3">
                                            <span
                                                class="avatar-title rounded-circle {{ $idx == 0 ? 'bg-warning' : ($idx == 1 ? 'bg-secondary' : 'bg-info bg-soft text-info') }} font-size-16">
                                                {{ $idx + 1 }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $ts->name }}</h6>
                                            <small class="text-muted">Rp
                                                {{ number_format($ts->total, 0, ',', '.') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                                @if ($topSpendingDept->isEmpty())
                                    <p class="text-muted text-center py-4">Belum ada data pengeluaran</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ TAB 4: USER & PESERTA ═══════════ --}}
        <div class="tab-pane" id="tab-user" role="tabpanel">
            <div class="row mt-3">
                {{-- KPI row --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="kpi-icon bg-primary bg-soft text-primary mx-auto mb-3"><i class="bx bx-user"></i>
                            </div>
                            <p class="text-muted mb-1">Rata-rata Peserta / Event</p>
                            <h3>{{ $avgPeserta }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="kpi-icon bg-success bg-soft text-success mx-auto mb-3"><i
                                    class="bx bx-check-circle"></i></div>
                            <p class="text-muted mb-1">Tingkat Kehadiran</p>
                            <h3>{{ $kehadiranPct }}%</h3>
                            <small class="text-muted">{{ $hadirPeserta }} / {{ $totalPeserta }} peserta</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="kpi-icon bg-info bg-soft text-info mx-auto mb-3"><i class="bx bx-group"></i></div>
                            <p class="text-muted mb-1">Total User Terdaftar</p>
                            <h3>{{ \App\Models\User::count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- User per Departemen (Donut) --}}
                <div class="col-xl-5">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">User per Departemen</h4>
                            <div id="chart-user-dept" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                {{-- Top 10 Peserta Aktif (Bar) --}}
                <div class="col-xl-7">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Top 10 Peserta Paling Aktif</h4>
                            <div id="chart-top-peserta" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}

@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var colors = ['#556ee6', '#34c38f', '#f1b44c', '#f46a6a', '#50a5f1', '#74788d', '#e83e8c', '#6f42c1',
                '#20c997', '#fd7e14'
            ];

            // ── Helper: generate last 12 month labels ──
            function getLast12Months() {
                var months = [];
                var now = new Date();
                for (var i = 11; i >= 0; i--) {
                    var d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                    months.push(d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'));
                }
                return months;
            }
            var monthLabels = getLast12Months();
            var monthShort = monthLabels.map(function(m) {
                var parts = m.split('-');
                var names = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                    'Des'
                ];
                return names[parseInt(parts[1]) - 1] + ' ' + parts[0].slice(2);
            });

            // ═══ TAB 1: EVENT ═══

            // 1. Event per Status (Donut)
            var statusData = @json($eventByStatus);
            var statusLabels = @json(\App\Models\event\Event::STATUS_LABELS);
            var sKeys = Object.keys(statusData);
            new ApexCharts(document.querySelector("#chart-event-status"), {
                chart: {
                    type: 'donut',
                    height: 300
                },
                series: sKeys.map(function(k) {
                    return statusData[k];
                }),
                labels: sKeys.map(function(k) {
                    return statusLabels[k] || 'Status ' + k;
                }),
                colors: colors,
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '55%'
                        }
                    }
                }
            }).render();

            // 2. Tren Event per Bulan (Line)
            var trendData = @json($eventTrend);
            new ApexCharts(document.querySelector("#chart-event-trend"), {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Event',
                    data: monthLabels.map(function(m) {
                        return trendData[m] || 0;
                    })
                }],
                xaxis: {
                    categories: monthShort
                },
                colors: ['#556ee6'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 4
                }
            }).render();

            // 3. Event per Departemen (Horizontal Bar)
            var deptData = @json($eventByDept);
            new ApexCharts(document.querySelector("#chart-event-dept"), {
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Event',
                    data: deptData.map(function(d) {
                        return d.total;
                    })
                }],
                xaxis: {
                    categories: deptData.map(function(d) {
                        return d.name;
                    })
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '50%',
                        borderRadius: 4
                    }
                },
                colors: ['#34c38f']
            }).render();

            // ═══ TAB 2: KONSUMSI ═══

            // 6. Top 10 Makanan (Bar)
            var topFoodsData = @json($topFoods);
            if (topFoodsData.length > 0) {
                new ApexCharts(document.querySelector("#chart-top-foods"), {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Qty Dipesan',
                        data: topFoodsData.map(function(f) {
                            return parseInt(f.total_qty);
                        })
                    }],
                    xaxis: {
                        categories: topFoodsData.map(function(f) {
                            return f.name;
                        })
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%'
                        }
                    },
                    colors: ['#f1b44c'],
                    dataLabels: {
                        enabled: true
                    }
                }).render();
            }

            // 7. Pengeluaran per Bulan (Area)
            var spendData = @json($monthlySpending);
            new ApexCharts(document.querySelector("#chart-monthly-spending"), {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Pengeluaran',
                    data: monthLabels.map(function(m) {
                        return parseFloat(spendData[m] || 0);
                    })
                }],
                xaxis: {
                    categories: monthShort
                },
                colors: ['#f46a6a'],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(v) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                        }
                    }
                }
            }).render();

            // 8. Distribusi Harga (Donut)
            var priceRanges = @json($priceRanges);
            new ApexCharts(document.querySelector("#chart-price-dist"), {
                chart: {
                    type: 'donut',
                    height: 300
                },
                series: Object.values(priceRanges),
                labels: Object.keys(priceRanges),
                colors: ['#34c38f', '#556ee6', '#f1b44c', '#f46a6a', '#74788d'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%'
                        }
                    }
                }
            }).render();

            // ═══ TAB 3: SALDO ═══

            // 11. Saldo per Dept (Bar)
            var saldoDept = @json($saldoByDept);
            if (saldoDept.length > 0) {
                new ApexCharts(document.querySelector("#chart-saldo-dept"), {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Saldo',
                        data: saldoDept.map(function(s) {
                            return parseFloat(s.saldo);
                        })
                    }],
                    xaxis: {
                        categories: saldoDept.map(function(s) {
                            return s.name;
                        })
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%'
                        }
                    },
                    colors: ['#50a5f1'],
                    yaxis: {
                        labels: {
                            formatter: function(v) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                            }
                        }
                    }
                }).render();
            }

            // 12. Mutasi Saldo (Line)
            var mutasiData = @json($saldoMutasi);
            new ApexCharts(document.querySelector("#chart-saldo-mutasi"), {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Mutasi',
                    data: monthLabels.map(function(m) {
                        return parseFloat(mutasiData[m] || 0);
                    })
                }],
                xaxis: {
                    categories: monthShort
                },
                colors: ['#34c38f'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 4
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                        }
                    }
                }
            }).render();

            // 13. Anggaran vs Pengeluaran (Grouped Bar)
            var budgetData = @json($deptBudget);
            if (budgetData.length > 0) {
                new ApexCharts(document.querySelector("#chart-budget-vs-spending"), {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: 'Saldo',
                            data: budgetData.map(function(d) {
                                return d.saldo;
                            })
                        },
                        {
                            name: 'Pengeluaran',
                            data: budgetData.map(function(d) {
                                return d.spending;
                            })
                        }
                    ],
                    xaxis: {
                        categories: budgetData.map(function(d) {
                            return d.name;
                        })
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '40%'
                        }
                    },
                    colors: ['#50a5f1', '#f46a6a'],
                    yaxis: {
                        labels: {
                            formatter: function(v) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                            }
                        }
                    }
                }).render();
            }

            // ═══ TAB 4: USER ═══

            // 15. User per Dept (Donut)
            var userDept = @json($userByDept);
            if (userDept.length > 0) {
                new ApexCharts(document.querySelector("#chart-user-dept"), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    series: userDept.map(function(u) {
                        return u.total;
                    }),
                    labels: userDept.map(function(u) {
                        return u.name;
                    }),
                    colors: colors,
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '55%'
                            }
                        }
                    }
                }).render();
            }

            // 16. Top 10 Peserta (Horizontal Bar)
            var topPes = @json($topPeserta);
            if (topPes.length > 0) {
                new ApexCharts(document.querySelector("#chart-top-peserta"), {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Keikutsertaan',
                        data: topPes.map(function(p) {
                            return p.total;
                        })
                    }],
                    xaxis: {
                        categories: topPes.map(function(p) {
                            return p.name;
                        })
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '50%',
                            borderRadius: 4
                        }
                    },
                    colors: ['#556ee6'],
                    dataLabels: {
                        enabled: true
                    }
                }).render();
            }

            // ── Re-render charts on tab show (ApexCharts needs visible container) ──
            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function() {
                    window.dispatchEvent(new Event('resize'));
                });
            });
        });
    </script>
@endsection
