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
                POS Sale | <span><strong class="text-secondary">Invoice</strong></span>
            </h3>

            <div class="row">

                <div class="col col-md-8">

                    <div class="card">
                        <div class="bard-body">

                            <h5 class="card-title m-3">Transaction Details</h5>
                            <div class="row">
                                <div class="col col-md-4">
                                    <p class="ml-4">Invoice Number : <span class="font-bold"> {{ $sale->invoice_number }} </span> </p>
                                    <p class="ml-4">Date : {{ date_format($sale->created_at, 'Y-m-d') }}</p>
                                </div>

                                <div class="col col-md-4">
                                    <p class="ml-3">Status : <span class="text-success font-bold">Completed</span> </p>
                                    <p class="ml-3">Sold By : {{ $sale->creator->name }} </p>
                                </div>
                            </div>
                            
                            <h5 class="card-title m-3">Sold Items</h5>
                            <div class="row">
                                <div class="col col-md-8">
                                    <table class="table table-condensed table-sm m-3">
                                        <thead>
                                            <tr>
                                                {{-- <th>#</th> --}}
                                                <th>Medicine</th>
                                                <th>Qty</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>

                                                @foreach ($sale->items as $item)
                                                    <td>{{ $item->medicine->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->medicine->pack_unit }}</td>
                                                    <td>{{ number_format($item->unit_price) }}</td>
                                                    <td>{{ number_format($item->subtotal) }}</td>
                                                @endforeach

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <h5 class="card-title m-3">Payment</h5>
                            <div class="row">
                                <div class="col col-md-8">
                                    <p class="ml-4">Payment Method : {{ $sale->payment->payment_method }}</p>
                                    <p class="ml-4">Amount: {{ number_format($sale->payment->amount) }}</p>
                                    <p></p>
                                </div>
                            </div>

                            <h5 class="card-title m-3">Batch Details</h5>

                            <div class="row m-1">
                                <div class="col-12">

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">

                                            <thead>
                                                <tr>
                                                    <th>Medicine</th>
                                                    <th>Batch</th>
                                                    <th>Expiry</th>
                                                    <th class="text-end">Qty</th>
                                                    <th class="text-end">Batch Selling Price</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach ($sale->items as $item)

                                                    @foreach ($item->batches as $batch)

                                                        <tr>
                                                            <td>
                                                                {{ $item->medicine->name }}
                                                            </td>

                                                            <td>
                                                                {{ $batch->medicineBatch->batch_number }}
                                                            </td>

                                                            <td>
                                                                {{ date_format($batch->medicineBatch->expires_at, 'Y-m-d') }}
                                                            </td>

                                                            <td class="text-end">
                                                                {{ $batch->quantity }}
                                                                {{ $item->medicine->base_unit }}
                                                            </td>

                                                            <td class="text-end">
                                                                TZS {{ number_format($batch->unit_price, 2) }}
                                                            </td>
                                                        </tr>

                                                    @endforeach

                                                @endforeach

                                            </tbody>

                                        </table>
                                    </div>


                                    {{-- Total --}}
                                    <div class="d-flex justify-content-end mt-3">

                                        <div style="min-width: 250px;">

                                            <div class="d-flex justify-content-between pt-2">

                                                <strong>
                                                    Total
                                                </strong>

                                                <strong>
                                                    TZS {{ number_format($sale->total_amount, 2) }}
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>


                            <p class="m-3"> 
                                <span> <a href="" class="btn btn-sm btn-success"> Print Invoice </a> </span> 
                                <span> <a href="{{ route('sales.create') }}" class="btn btn-sm btn-secondary"> New Sale </a> </span>
                                <span> <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary"> Sales List </a> </span>
                            </p>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


</body>

</html>