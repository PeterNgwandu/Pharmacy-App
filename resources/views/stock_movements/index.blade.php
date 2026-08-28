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
                <h3 class="mt-5">Stock Movements Listings
                </h3>

                <table id="medicinesBatchTable" class="table table-sm table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Batch Number</th>
                                <th>Movement Type</th>
                                <th>Note</th>
                                <th>Quantity</th>
                                <th>User</th>
                                <th>Movement Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockMovements as $stockMovement)
                                <tr>
                                    <td>{{ $stockMovement->id }}</td>
                                    <td>{{ $stockMovement->medicine->name }}</td>
                                    <td>{{ $stockMovement->medicineBatch->batch_number }}</td>
                                    <td>{{ $stockMovement->type }}</td>
                                    <td>{{ $stockMovement->notes }}</td>
                                    <td>{{ $stockMovement->quantity }}</td>
                                    <td>{{ $stockMovement->creator->username }}</td>
                                    <td>{{ $stockMovement->created_at->format('d M Y') }}</td>
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