<div class="table-responsive">
    <table class="table align-middle table-nowrap table-hover">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 70px;">#</th>
                <th scope="col">Action</th>
                <th scope="col">Departemen</th>
                <th scope="col">Code</th>
                <th scope="col">Saldo</th>
                <th scope="col">Last Updated</th>
                <th scope="col">Updated By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($departements as $key => $departemen)
                <tr>
                    <td>{{ $departements->firstItem() + $key }}</td>
                    <td>
                        <ul class="list-inline font-size-20 contact-links mb-0">
                            @can('create saldo transaction')
                                <li class="list-inline-item px-2">
                                    <a href="javascript:void(0);" class="btn-transaksi" data-uuid="{{ $departemen->uuid }}"
                                        data-departemen="{{ $departemen->name }}" title="Tambah Saldo"><i
                                            class="bx bx-plus-circle text-success"></i></a>
                                </li>
                            @endcan
                            <li class="list-inline-item px-2">
                                <a href="javascript:void(0);" class="btn-log" data-uuid="{{ $departemen->uuid }}"
                                    data-departemen="{{ $departemen->name }}" title="Lihat Log"><i
                                        class="bx bx-list-ul text-info"></i></a>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <h5 class="font-size-14 mb-1">
                            <a href="javascript:void(0);" class="text-dark">{{ $departemen->name }}</a>
                        </h5>
                    </td>
                    <td>{{ $departemen->code_departement }}</td>
                    <td>
                        @php $saldoAmount = $departemen->saldo ? $departemen->saldo->saldo : 0; @endphp
                        <span
                            class="fw-bold {{ $saldoAmount > 0 ? 'text-success' : ($saldoAmount < 0 ? 'text-danger' : 'text-muted') }}">
                            Rp {{ number_format($saldoAmount, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>{{ $departemen->saldo && $departemen->saldo->updated_at ? $departemen->saldo->updated_at->format('d M Y H:i') : '-' }}
                    </td>
                    <td>{{ $departemen->saldo->updated_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-center mt-4">
            {!! $departements->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>
