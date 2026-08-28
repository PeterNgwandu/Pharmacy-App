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
                <h3 class="mt-5">Batches | FEFO Testing 
                </h3>

                <form method="GET" action="{{ route('medicine_batches.fefo') }}">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label for="medicine_id" class="form-label">
                                Medicine Batch Details
                            </label>

                            <select
                                name="medicine_id"
                                id="medicine_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Medicine
                                </option>

                                @foreach ($medicines as $medicine)

                                    <option
                                        value="{{ $medicine->id }}"
                                        {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}
                                    >
                                        {{ $medicine->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2 mb-3 d-flex align-items-end">

                            <button type="submit" class="btn btn-primary">
                                Check FEFO
                            </button>

                        </div>

                    </div>

                </form>

                <table id="medicinesBatchTable"
                    class="table table-sm table-condensed table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Batch Number</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($batches as $batch)

                            <tr>
                                <td>{{ $batch->id }}</td>

                                <td>
                                    {{ $batch->batch_number }}
                                </td>

                                <td>
                                    {{ $batch->expires_at->format('d-M-Y') }}
                                </td>

                                <td>
                                    {{ number_format($batch->quantity, 0) }}
                                    {{ $batch->medicine->base_unit }}
                                </td>

                                <td>
                                    {{ $batch->status ? 'Active' : 'Inactive' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center">
                                    No available batches found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </body>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#medicinesBatchTable', {
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>

</html>