<div class="row">
    @forelse ($foods as $food)
        <div class="col-xl-4 col-sm-6">
            <div class="card food-card">
                <div class="card-body">
                    <div class="product-img position-relative">
                        {{-- Status Badge --}}
                        @if ($food->is_active)
                            <span class="badge bg-success badge-status">Aktif</span>
                        @else
                            <span class="badge bg-secondary badge-status">Nonaktif</span>
                        @endif

                        {{-- Stock Badge --}}
                        @if ($food->qty_available > 0)
                            <span class="badge bg-info badge-stock">Stok: {{ $food->qty_available }}</span>
                        @else
                            <span class="badge bg-danger badge-stock">Habis</span>
                        @endif

                        <div class="food-img-wrapper">
                            @if ($food->image)
                                <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}"
                                    class="img-fluid">
                            @else
                                <img src="{{ URL::asset('/assets/images/product/img-' . (($loop->index % 8) + 1) . '.png') }}"
                                    alt="{{ $food->name }}" class="img-fluid" style="opacity:0.3;">
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1 text-truncate">
                            <a href="javascript:void(0);" class="text-dark">{{ $food->name }}</a>
                        </h5>
                        <p class="text-muted mb-2 small text-truncate">{{ $food->description ?? 'Tidak ada deskripsi' }}
                        </p>
                        <h5 class="my-0 text-primary fw-bold">{{ $food->formatted_price }}</h5>
                    </div>
                    <div class="mt-3 d-flex justify-content-center gap-2">
                        @can('edit food')
                            <button class="btn btn-sm btn-outline-info btn-edit-food" data-uuid="{{ $food->uuid }}"
                                title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-stock-food" data-uuid="{{ $food->uuid }}"
                                data-name="{{ $food->name }}" data-qty="{{ $food->qty_available }}" title="Tambah Stok">
                                <i class="bx bx-plus-circle"></i>
                            </button>
                        @endcan
                        <button class="btn btn-sm btn-outline-warning btn-log-food" data-uuid="{{ $food->uuid }}"
                            data-name="{{ $food->name }}" title="Log">
                            <i class="bx bx-list-ul"></i>
                        </button>
                        @can('delete food')
                            <button class="btn btn-sm btn-outline-danger btn-delete-food" data-uuid="{{ $food->uuid }}"
                                data-name="{{ $food->name }}" title="Hapus">
                                <i class="bx bx-trash"></i>
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-food-menu text-muted" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-muted">Belum ada data makanan</h5>
                    <p class="text-muted">Klik tombol "Tambah" untuk menambahkan makanan baru.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if ($foods->hasPages())
    <div class="row">
        <div class="col-lg-12">
            <ul class="pagination pagination-rounded justify-content-center mt-3 mb-4 pb-1">
                {{-- Previous --}}
                @if ($foods->onFirstPage())
                    <li class="page-item disabled"><span class="page-link"><i class="mdi mdi-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $foods->previousPageUrl() }}"><i
                                class="mdi mdi-chevron-left"></i></a></li>
                @endif

                {{-- Pages --}}
                @foreach ($foods->getUrlRange(1, $foods->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $foods->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                {{-- Next --}}
                @if ($foods->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $foods->nextPageUrl() }}"><i
                                class="mdi mdi-chevron-right"></i></a></li>
                @else
                    <li class="page-item disabled"><span class="page-link"><i class="mdi mdi-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif
