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
                <h3 class="mt-5">Medicines Batch Listings |  <span><a class="btn btn-sm btn-success mb-1" href="{{ route('medicines_batch.create') }}">Receive Medicines Form</a></span>
                </h3>

                <table id="medicinesBatchTable" class="table table-sm table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Batch Number</th>
                                <th>Manufactured</th>
                                <th>Expiry</th>
                                <th>Qty</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $batch)

                                @php
                                    $today = now()->startOfDay();

                                    $expiryDate = \Carbon\Carbon::parse($batch->expires_at);

                                    $daysToExpiry = $today->diffInDays(
                                        $expiryDate,
                                        false
                                    );
                                @endphp

                                <tr>
                                    <td>{{ $batch->id }}</td>
                                    <td>{{ $batch->medicine->name }}</td>
                                    <td>{{ $batch->batch_number }}</td>
                                    <td>{{ $batch->manufactured_at ? $batch->manufactured_at->format('d M Y'): '-' }}</td>
                                    <td>
                                        {{ $batch->expires_at->format('d M Y') }}

                                        

                                        @if($daysToExpiry < 0)

                                            <span class="badge bg-danger">
                                                Expired
                                            </span>

                                        @elseif($daysToExpiry <= 90)

                                            <span class="badge bg-warning text-dark">
                                                Expiring Soon
                                            </span>

                                        @else

                                            <span class="badge bg-success">
                                                Valid
                                            </span>

                                        @endif
                                    </td>
                                    <td>{{ number_format($batch->quantity, 0) }}</td>
                                    <td>{{ number_format($batch->purchase_price, 0) }}</td>
                                    <td>{{ number_format($batch->selling_price, 0) }}</td>
                                    <td>
                                        @if($batch->status)

                                            <span class="badge bg-success">
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                Inactive
                                            </span>

                                        @endif
                                    </td>

                                    {{-- Actions --}}
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
                                                        href="{{ route('medicines_batch.edit', $batch->id) }}"
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
                                                        action="{{ route('medicines_batch.destroy', $batch->id) }}"
                                                        id="delete-medicine-batch-{{ $batch->id }}"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="button"
                                                            class="dropdown-item text-danger"
                                                            onclick="confirmDeleteMedicineBatch({{ $batch->id }})"
                                                        >
                                                            Delete
                                                        </button>

                                                    </form>

                                                </li>

                                            </ul>

                                        </div>

                                    </td>

                                </tr>
                                </tr>
                            @endforeach
                            
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