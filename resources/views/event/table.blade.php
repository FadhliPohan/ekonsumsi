<div class="table-responsive">
    <table class="table align-middle table-nowrap table-hover">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">Action</th>
                <th scope="col">Nama Event</th>
                <th scope="col">Departemen</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Lokasi</th>
                <th scope="col">Status</th>
                <th scope="col">Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $key => $event)
                <tr>
                    <td>{{ $events->firstItem() + $key }}</td>
                    <td>
                        <ul class="list-inline font-size-20 contact-links mb-0">
                            <li class="list-inline-item px-1">
                                <a href="javascript:void(0);" class="btn-show-event text-primary"
                                    data-uuid="{{ $event->uuid }}" title="Detail">
                                    <i class="bx bx-show-alt"></i>
                                </a>
                            </li>
                            @can('edit events')
                                @if ($event->isEditable())
                                    <li class="list-inline-item px-1">
                                        <a href="{{ route('event.edit', $event->uuid) }}" class="text-warning"
                                            title="Edit">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                    </li>
                                @endif
                            @endcan
                            @can('delete events')
                                @if ($event->isDeletable())
                                    <li class="list-inline-item px-1">
                                        <a href="javascript:void(0);" class="btn-delete-event text-danger"
                                            data-uuid="{{ $event->uuid }}" data-name="{{ $event->name }}" title="Hapus">
                                            <i class="bx bx-trash-alt"></i>
                                        </a>
                                    </li>
                                @endif
                            @endcan
                        </ul>
                    </td>
                    <td>
                        <h5 class="font-size-14 mb-1">
                            <a href="javascript:void(0);" class="text-dark btn-show-event"
                                data-uuid="{{ $event->uuid }}">{{ $event->name }}</a>
                        </h5>
                    </td>
                    <td>{{ $event->name_departemen }}</td>
                    <td>
                        {{ $event->start_date ? $event->start_date->format('d M Y') : '-' }}
                        @if ($event->end_date)
                            <br><small class="text-muted">s/d {{ $event->end_date->format('d M Y') }}</small>
                        @endif
                    </td>
                    <td>{{ $event->location ?? '-' }}</td>
                    <td>
                        @php
                            $statusLabels = \App\Models\event\Event::STATUS_LABELS;
                            $statusBadges = \App\Models\event\Event::STATUS_BADGES;
                        @endphp
                        <span class="badge {{ $statusBadges[$event->status] ?? 'badge-soft-secondary' }} font-size-11">
                            {{ $statusLabels[$event->status] ?? 'Unknown' }}
                        </span>
                    </td>
                    <td>{{ $event->name_user_created }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Belum ada event.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-center mt-4">
            {!! $events->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>
