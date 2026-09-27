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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
          rel="stylesheet" />
          

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">

        @include('layouts.navigation')


        <div class="mt-10 mr-10 ml-10">

            {{-- Page Header --}}
            <h3 class="mt-5 mb-4">
                POS Sale List</span>
            </h3>

            <div class="row">
                <div class="col col-md-12">

                    <div class="row mb-4">
                        <div class="col-12">

                            <h5 class="text-muted mb-3">Custom Filter</h5>

                            <form action="{{ route('sales.index') }}" method="GET">

                                <div class="row g-3">

                                    {{-- General Search --}}
                                    <div class="col-md-3">
                                        <label for="search" class="form-label">
                                            Invoice No / General Search
                                        </label>

                                        <input
                                            type="text"
                                            name="search"
                                            id="search"
                                            class="form-control"
                                            placeholder="Invoice No..."
                                            value="{{ request('search') }}"
                                        >
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-2">
                                        <label for="fromDate" class="form-label">
                                            From Date
                                        </label>

                                        <input
                                            type="date"
                                            name="from_date"
                                            id="fromDate"
                                            class="form-control"
                                            value="{{ request('from_date') }}"
                                        >
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-2">
                                        <label for="toDate" class="form-label">
                                            To Date
                                        </label>

                                        <input
                                            type="date"
                                            name="to_date"
                                            id="toDate"
                                            class="form-control"
                                            value="{{ request('to_date') }}"
                                        >
                                    </div>

                                    {{-- Payment Method --}}
                                    <div class="col-md-2">
                                        <label for="paymentMethod" class="form-label">
                                            Payment Method
                                        </label>

                                        <select
                                            name="payment_method"
                                            id="paymentMethod"
                                            class="form-select"
                                        >
                                            <option value="">All Payments</option>

                                            <option value="CASH"
                                                {{ request('payment_method') == 'CASH' ? 'selected' : '' }}>
                                                CASH
                                            </option>

                                            <option value="LIPA_HAPA"
                                                {{ request('payment_method') == 'LIPA_HAPA' ? 'selected' : '' }}>
                                                LIPA HAPA
                                            </option>

                                            <option value="BANK"
                                                {{ request('payment_method') == 'BANK' ? 'selected' : '' }}>
                                                BANK
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="col-md-3 d-flex align-items-end gap-2">

                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-search">&nbsp; </i>Search
                                        </button>

                                        <a
                                            href="{{ route('sales.index') }}"
                                            class="btn btn-secondary"
                                        >
                                            <i class="bi bi-x"></i>
                                            Reset
                                        </a>

                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>

                    <table id="salesList" class="table table-condensed table-striped table-responsive table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date & Time</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Sold By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)

                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $sale->items->count() }}</td>
                                        <td>{{ number_format($sale->total_amount) }}</td>
                                        <td>{{ $sale->payment->payment_method }}</td>
                                        <td>{{ $sale->creator->name }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-success" href="{{ route('sales.show', $sale->id) }}">View Sale</a>
                                        </td>
                                    </tr>
                                
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $sales->links() }}
                    </div>

                </div>
            </div>

        </div>

    </div>


</body>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#salesList', {
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                responsive: true
            });
        });
    </script>

</html>