@extends('layouts.app_layout')

@section('content')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Supplier Account Summary</h3>
            <p class="text-muted mb-0">
                Monitor supplier purchases, payments, and outstanding balances.
            </p>
        </div>
    </div>

    {{-- Overall Summary --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Purchases</h6>
                    <h4>
                        TZS {{ number_format($summary['total_purchases'], 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Paid</h6>
                    <h4 class="text-success">
                        TZS {{ number_format($summary['total_paid'], 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Outstanding Balance</h6>
                    <h4 class="text-danger">
                        TZS {{ number_format($summary['outstanding_balance'], 2) }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- Search and Filters --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('supplier_accounts.index') }}">

                <div class="row align-items-end">

                    {{-- Supplier Dropdown --}}
                    <div class="col-md-5 mb-3">
                        <label for="supplier_id" class="form-label">
                            Supplier
                        </label>

                        <select
                            name="supplier_id"
                            id="supplier_id"
                            class="form-select"
                        >
                            <option value="">All Suppliers</option>

                            @foreach ($supplierOptions as $option)
                                <option
                                    value="{{ $option->id }}"
                                    {{ (string) $supplierId === (string) $option->id ? 'selected' : '' }}
                                >
                                    {{ $option->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="payment_status" class="form-label">
                            Payment Status
                        </label>

                        <select
                            name="payment_status"
                            id="payment_status"
                            class="form-select"
                        >
                            <option value="">All Payment Statuses</option>

                            <option value="unpaid"
                                {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>
                                Unpaid
                            </option>

                            <option value="partial"
                                {{ $paymentStatus === 'partial' ? 'selected' : '' }}>
                                Partially Paid
                            </option>

                            <option value="paid"
                                {{ $paymentStatus === 'paid' ? 'selected' : '' }}>
                                Fully Paid
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel"></i> Filter
                        </button>

                
                    <a href="{{ route('supplier_accounts.index') }}"
                    class="btn btn-secondary flex-fill">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                    

                    </div>


                </div>

            </form>

        </div>

    </div>

    {{-- Supplier Account Table --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">Supplier Accounts</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-striped align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Phone</th>
                            <th>Total Purchases</th>
                            <th>Total Paid</th>
                            <th>Outstanding Balance</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($suppliers as $supplier)

                            @php
                                $supplierPurchases = (float) $supplier->total_purchases;
                                $supplierPaid = (float) $supplier->total_paid;

                                $supplierOutstanding = max(
                                    0,
                                    round($supplierPurchases - $supplierPaid, 2)
                                );

                                if ($supplierPurchases <= 0 || $supplierPaid <= 0) {
                                    $supplierPaymentStatus = 'Unpaid';
                                } elseif ($supplierOutstanding > 0) {
                                    $supplierPaymentStatus = 'Partially Paid';
                                } else {
                                    $supplierPaymentStatus = 'Paid';
                                }
                            @endphp

                            <tr>

                                <td>
                                    {{ $suppliers->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>{{ $supplier->name }}</strong>
                                </td>

                                <td>
                                    {{ $supplier->phone ?: '—' }}
                                </td>

                                <td class="text-nowrap">
                                    TZS {{ number_format($supplierPurchases, 2) }}
                                </td>

                                <td class="text-nowrap text-success">
                                    TZS {{ number_format($supplierPaid, 2) }}
                                </td>

                                <td class="text-nowrap {{ $supplierOutstanding > 0 ? 'text-danger' : 'text-success' }}">
                                    TZS {{ number_format($supplierOutstanding, 2) }}
                                </td>

                                <td>
                                    @if ($supplierPaymentStatus === 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif ($supplierPaymentStatus === 'Partially Paid')
                                        <span class="badge bg-warning text-dark">
                                            Partially Paid
                                        </span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>

                                <td>
                                    <a
                                        href="{{ route('suppliers.show', $supplier->id) }}"
                                        class="btn btn-sm btn-outline-success"
                                    >
                                        View Supplier
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    No supplier accounts found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $suppliers->links() }}
            </div>

        </div>

    </div>

</div>

@endsection