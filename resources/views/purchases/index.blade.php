@extends('layouts.app_layout')

@section('content') <div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Purchases</h4>
            <p class="text-muted mb-0">
                Manage and view medicine purchases.
            </p>
        </div>

        <a href="{{ route('purchases.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i>
            New Purchase
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">

            <form action="{{ route('purchases.index') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Search --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="Purchase number or supplier..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    {{-- From Date --}}
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            id="from_date"
                            class="form-control"
                            value="{{ request('from_date') }}"
                        >
                    </div>

                    {{-- To Date --}}
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            id="to_date"
                            class="form-control"
                            value="{{ request('to_date') }}"
                        >
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            <i class="bi bi-search">&nbsp;</i>
                            Search
                        </button>

                        <a
                            href="{{ route('purchases.index') }}"
                            class="btn btn-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Purchase Table --}}
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-sm table-bordered align-middle">

                    <thead>
                        <tr>
                            <th>Purchase No</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($purchases as $purchase)

                            <tr>

                                <td>
                                    {{ $purchase->purchase_number }}
                                </td>

                                <td>
                                    {{ $purchase->purchase_date->format('Y-m-d') }}
                                </td>

                                <td>
                                    {{ $purchase->supplier->name }}
                                </td>

                                <td>
                                    {{ $purchase->items->count() }}
                                </td>

                                <td>
                                    TZS {{ number_format($purchase->total_amount, 0) }}
                                </td>

                                <td>

                                    <span class="badge
                                        {{ $purchase->status === 'Received'
                                            ? 'bg-success'
                                            : 'bg-secondary' }}">

                                        {{ $purchase->status }}

                                    </span>

                                </td>

                                <td>
                                    {{ $purchase->creator->name ?? '-' }}
                                </td>

                                <td>

                                    <a
                                        href="{{ route('purchases.show', $purchase->id) }}"
                                        class="btn btn-sm btn-outline-success"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    No purchases found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $purchases->links() }}
            </div>

        </div>

    </div>

</div>


@endsection
