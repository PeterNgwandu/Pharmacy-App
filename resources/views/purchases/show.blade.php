@extends('layouts.app_layout')

@section('content')

<div class="container-fluid py-4">


{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Purchase Details</h3>
        <p class="text-muted mb-0">
            View purchase information and purchased medicines.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('purchases.index') }}"
            class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>


{{-- Purchase Information --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Purchase Information</h5>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Purchase Number</label>
                <div class="fw-bold">
                    {{ $purchase->purchase_number }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Purchase Date</label>
                <div class="fw-bold">
                    {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Status</label>
                <div>
                    @php
                        $statusClass = match (strtolower($purchase->status)) {
                            'received' => 'bg-success',
                            'pending' => 'bg-warning text-dark',
                            'partially received' => 'bg-info text-dark',
                            'cancelled' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp

                    <span class="badge {{ $statusClass }}">
                        {{ $purchase->status }}
                    </span>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Supplier</label>
                <div class="fw-bold">
                    {{ $purchase->supplier->name ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Recorded By</label>
                <div class="fw-bold">
                    {{ $purchase->creator->name ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Total Amount</label>
                <div class="fw-bold text-primary fs-5">
                    TZS {{ number_format((float) $purchase->total_amount, 2) }}
                </div>
            </div>

        </div>

    </div>
</div>


{{-- Supplier Contact Information --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Supplier Information</h5>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Supplier Name</label>
                <div>
                    {{ $purchase->supplier->name ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Phone</label>
                <div>
                    {{ $purchase->supplier->phone ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="text-muted small">Email</label>
                <div>
                    {{ $purchase->supplier->email ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-12 mb-0">
                <label class="text-muted small">Address</label>
                <div>
                    {{ $purchase->supplier->address ?? 'N/A' }}
                </div>
            </div>

        </div>

    </div>
</div>


{{-- Purchased Medicines --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Purchased Medicines</h5>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Supplier Batch No.</th>
                        <th>Manufactured Date</th>
                        <th>Expiry Date</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Purchase Price</th>
                        <th class="text-end">Selling Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($purchase->items as $item)

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $item->medicine->name ?? 'Medicine unavailable' }}
                                </div>

                                @if (!empty($item->medicine?->strength))
                                    <small class="text-muted">
                                        {{ $item->medicine->strength }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $item->batch_number ?: 'N/A' }}
                            </td>

                            <td>
                                {{ $item->manufactured_at
                                    ? \Carbon\Carbon::parse($item->manufactured_at)->format('d M Y')
                                    : 'N/A' }}
                            </td>

                            <td>
                                {{ $item->expires_at
                                    ? \Carbon\Carbon::parse($item->expires_at)->format('d M Y')
                                    : 'N/A' }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item->quantity) }}
                            </td>

                            <td class="text-end">
                                {{ number_format((float) $item->purchase_price, 2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format((float) $item->selling_price, 2) }}
                            </td>

                            <td class="text-end fw-semibold">
                                {{ number_format((float) $item->subtotal, 2) }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No medicines found for this purchase.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if ($purchase->items->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-end">
                                Grand Total:
                            </th>

                            <th class="text-end text-primary">
                                TZS {{ number_format((float) $purchase->total_amount, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                @endif

            </table>

        </div>

    </div>
</div>

{{-- Supplier Payment Summary --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">Supplier Payment Summary</h5>
    </div>

    <div class="card-body">
        <div class="row">

            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-muted">Purchase Total</h6>

                    <h4 class="mb-0">
                        TZS {{ number_format((float) $purchase->total_amount, 2) }}
                    </h4>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-muted">Total Paid</h6>

                    <h4 class="text-success mb-0">
                        TZS {{ number_format((float) $totalPaid, 2) }}
                    </h4>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-muted">Outstanding Balance</h6>

                    <h4 class="{{ $outstandingBalance > 0 ? 'text-danger' : 'text-success' }} mb-0">
                        TZS {{ number_format((float) $outstandingBalance, 2) }}
                    </h4>
                </div>
            </div>

        </div>

        <div class="mt-2">
            <strong>Payment Status:</strong>

            @if ($paymentStatus === 'Paid')
                <span class="badge bg-success">Paid</span>
            @elseif ($paymentStatus === 'Partially Paid')
                <span class="badge bg-warning text-dark">Partially Paid</span>
            @else
                <span class="badge bg-danger">Unpaid</span>
            @endif
        </div>
    </div>
</div>


{{-- Record Supplier Payment --}}
@if ($outstandingBalance > 0)
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <h5 class="mb-0">Record Supplier Payment</h5>
        </div>

        <div class="card-body">

            <form
                action="{{ route('supplier_payments.store', $purchase->id) }}"
                method="POST"
            >
                @csrf

                <div class="row">

                    {{-- Payment Amount --}}
                    <div class="col-md-4 mb-3">
                        <label for="amount" class="form-label">
                            Payment Amount (TZS) <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            min="0.01"
                            max="{{ number_format($outstandingBalance, 2, '.', '') }}"
                            step="0.01"
                            value="{{ old('amount') }}"
                            required
                        >

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Outstanding:
                            TZS {{ number_format($outstandingBalance, 2) }}
                        </small>
                    </div>

                    {{-- Payment Method --}}
                    <div class="col-md-4 mb-3">
                        <label for="payment_method" class="form-label">
                            Payment Method <span class="text-danger">*</span>
                        </label>

                        <select
                            name="payment_method"
                            id="payment_method"
                            class="form-select @error('payment_method') is-invalid @enderror"
                            required
                        >
                            <option value="">Select Payment Method</option>

                            <option value="CASH"
                                {{ old('payment_method') === 'CASH' ? 'selected' : '' }}>
                                Cash
                            </option>

                            <option value="LIPA_HAPA"
                                {{ old('payment_method') === 'LIPA_HAPA' ? 'selected' : '' }}>
                                Lipa Hapa
                            </option>

                            <option value="BANK"
                                {{ old('payment_method') === 'BANK' ? 'selected' : '' }}>
                                Bank Transfer
                            </option>
                        </select>

                        @error('payment_method')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Payment Date --}}
                    <div class="col-md-4 mb-3">
                        <label for="payment_date" class="form-label">
                            Payment Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="payment_date"
                            id="payment_date"
                            class="form-control @error('payment_date') is-invalid @enderror"
                            value="{{ old('payment_date', today()->format('Y-m-d')) }}"
                            required
                        >

                        @error('payment_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Payment Reference --}}
                    <div class="col-md-6 mb-3">
                        <label for="payment_reference" class="form-label">
                            Payment Reference
                        </label>

                        <input
                            type="text"
                            name="payment_reference"
                            id="payment_reference"
                            class="form-control @error('payment_reference') is-invalid @enderror"
                            value="{{ old('payment_reference') }}"
                            placeholder="e.g. Bank transaction or receipt number"
                        >

                        @error('payment_reference')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-6 mb-3">
                        <label for="notes" class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            id="notes"
                            rows="2"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Optional payment details"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cash-coin"></i>
                    Record Payment
                </button>

            </form>

        </div>
    </div>
@endif


{{-- Supplier Payment History --}}
<div class="card shadow-sm mb-4">

    <div class="card-header">
        <h5 class="mb-0">Supplier Payment History</h5>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-striped table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($purchase->payments->sortByDesc('payment_date') as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>

                            <td class="text-nowrap">
                                TZS {{ number_format((float) $payment->amount, 2) }}
                            </td>

                            <td>
                                @if ($payment->payment_method === 'CASH')
                                    Cash
                                @elseif ($payment->payment_method === 'LIPA_HAPA')
                                    Lipa Hapa
                                @elseif ($payment->payment_method === 'BANK')
                                    Bank Transfer
                                @else
                                    {{ $payment->payment_method }}
                                @endif
                            </td>

                            <td>
                                {{ $payment->payment_reference ?: '—' }}
                            </td>

                            <td>
                                {{ $payment->creator?->name ?? 'Unknown' }}
                            </td>

                            <td>
                                {{ $payment->notes ?: '—' }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No payments have been recorded for this purchase.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

                @if ($purchase->payments->isNotEmpty())
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="2" class="text-end">
                                Total Paid:
                            </th>

                            <th colspan="5">
                                TZS {{ number_format((float) $totalPaid, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                @endif

            </table>

        </div>

    </div>
</div>


{{-- Actions --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <a href="{{ route('purchases.index') }}"
        class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Purchases
    </a>

    @if (strtolower($purchase->status) === 'pending')
        <form 
            action="{{ route('purchases.receive', $purchase->id) }}", 
            method="POST"
            onsubmit="return confirm('Are you sure you want to receive this purchase? This will add the purchased medicines to inventory.');"
        >
            @csrf

            <button type="submit"
                class="btn btn-success"
                <i class="fas fa-box-open"></i> Receive Purchase
            </button>
        </form>
    @endif

</div>

</div>

@endsection
