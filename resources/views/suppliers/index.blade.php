@extends('layouts.app_layout')

@section('content') <div class="container-fluid py-4">


        <div class="mt-10 mr-10 ml-10">

            {{-- Page Header --}}
            <h3 class="mt-5 mb-4">
               <p>Suppliers Listings </p>
            </h3>

            <div class="row">
                <div class="col col-md-12">

                    <table class="table table-sm table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->id }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->creator->name }}</td>
                                    <td>
                                        <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $supplier->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">

                                            <button
                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                            >
                                                Actions
                                            </button>

                                            <ul class="dropdown-menu">

                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('suppliers.edit', $supplier->id) }}"
                                                    >
                                                        Edit
                                                    </a>
                                                </li>


                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                <li>

                                                    <form
                                                        method="POST"
                                                        action="{{ route('supplier.destroy', $supplier) }}"
                                                        id="delete-supplier-{{ $supplier->id }}"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="button"
                                                            class="dropdown-item text-danger"
                                                            onclick="confirmDeleteSupplier({{ $supplier->id }})"
                                                        >
                                                            Delete
                                                        </button>

                                                    </form>

                                                </li>

                                            </ul>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

    </div>


</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.add('hidden');
        }
    });

    window.addEventListener('beforeunload', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.remove('hidden');
        }
    });
</script>

</html>

@endsection