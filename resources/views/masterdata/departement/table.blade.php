<div class="table-responsive">
    <table class="table align-middle table-nowrap table-hover">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 70px;">#</th>
                <th scope="col">Action</th>
                <th scope="col">Name</th>
                <th scope="col">Code</th>
                <th scope="col">Location</th>
                <th scope="col">Status</th>
                <th scope="col">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($departements as $key => $departement)
                <tr>
                    <td>{{ $departements->firstItem() + $key }}</td>
                     <td>
                        <ul class="list-inline font-size-20 contact-links mb-0">
                            @can('edit departemen')
                                <li class="list-inline-item px-2">
                                    <a href="javascript:void(0);" class="edit-btn" data-uuid="{{ $departement->uuid }}"
                                        title="Edit"><i class="bx bx-pencil"></i></a>
                                </li>
                            @endcan
                            @can('delete departemen')
                                <li class="list-inline-item px-2">
                                    <a href="javascript:void(0);" class="delete-btn" data-uuid="{{ $departement->uuid }}"
                                        title="Delete"><i class="bx bx-trash-alt"></i></a>
                                </li>
                            @endcan
                        </ul>
                    </td>
                    <td>
                        <h5 class="font-size-14 mb-1"><a href="javascript: void(0);"
                                class="text-dark">{{ $departement->name }}</a></h5>
                    </td>
                    <td>{{ $departement->code_departement }}</td>
                    <td>{{ $departement->location }}</td>
                    <td>
                        @if ($departement->is_active)
                            <span class="badge badge-soft-success font-size-11">Active</span>
                        @else
                            <span class="badge badge-soft-danger font-size-11">Inactive</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($departement->description, 30) }}</td>
                   
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
