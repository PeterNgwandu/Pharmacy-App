<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="flash-success" content="{{ session('success') ?? '' }}">
        <meta name="flash-error" content="{{ session('error') ?? '' }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            
            <div class="mt-10 mr-10 ml-10">
                <h3 class="mt-5">Medicines List |  <span><a class="btn btn-sm btn-success mb-1" href="{{ route('medicines.create') }}">Create Medicines Form</a></span>
                </h3>

                <table id="medicinesTable" class="table table-sm table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                {{-- <th scope="col">Category ID</th> --}}
                                <th scope="col">Name</th>
                                <th scope="col">Generic Name</th>
                                <th scope="col">Brand Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Dosage Form</th>
                                <th scope="col">Strength</th>
                                <th scope="col">Base Unit</th>
                                <th scope="col">Pack Unit</th>
                                <th scope="col">Unit Per Pack</th>
                                <th scope="col">Reorder Level</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicines as $medicine)
                                <tr>
                                    {{-- <td>{{ $category->id }}</td> --}}
                                    <td>{{ $medicine->name }}</td>
                                    <td>{{ $medicine->generic_name }}</td>
                                    <td>{{ $medicine->brand_name }}</td>
                                    <td>{{ $medicine->category->name }}</td>
                                    <td>{{ $medicine->dosageForm->name }}</td>
                                    <td>{{ $medicine->strength }}</td>
                                    <td>{{ $medicine->base_unit }}</td>
                                    <td>{{ $medicine->pack_unit }}</td>
                                    <td>{{ $medicine->units_per_pack }}</td>
                                    <td>{{ $medicine->reorder_level }}</td>
                                    <td>{{ $medicine->creator->name }}</td>
                                    <td>
                                        @if ($medicine->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('medicines.edit', $medicine) }}" 
                                            <i class="bi bi-pen-fill text-warning"></i>
                                        </a>
                                        <form method="POST" action="{{ route('medicines.destroy', $medicine) }}" 
                                            id="delete-medicine-{{ $medicine->id }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="" onclick="confirmDeleteMedicine({{ $medicine->id }})">
                                                <i class="bi bi-trash3 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>

            </div>

        </div>

    </body>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#medicinesTable', {
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>

</html>