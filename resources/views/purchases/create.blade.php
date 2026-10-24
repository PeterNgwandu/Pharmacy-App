@extends('layouts.app_layout')

@section('content')

<div class="container-fluid py-4">


{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h4 class="mb-1">New Purchase</h4>

        <p class="text-muted mb-0">
            Record medicines purchased from a supplier.
        </p>
    </div>

    <a
        href="{{ route('purchases.index') }}"
        class="btn btn-secondary"
    >
        Back to Purchases
    </a>

</div>


{{-- Validation Errors --}}
@if ($errors->any())

    <div class="alert alert-danger">

        <strong>Please correct the following errors:</strong>

        <ul class="mb-0 mt-2">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<form
    action="{{ route('purchases.store') }}"
    method="POST"
    id="purchaseForm"
>

    @csrf


    {{-- Purchase Information --}}
    <div class="card mb-3">

        <div class="card-header">
            <strong>Purchase Information</strong>
        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Supplier --}}
                <div class="col-md-6">

                    <label
                        for="supplier_id"
                        class="form-label"
                    >
                        Supplier
                    </label>

                    <select
                        name="supplier_id"
                        id="supplier_id"
                        class="form-select @error('supplier_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Supplier
                        </option>

                        @foreach ($suppliers as $supplier)

                            <option
                                value="{{ $supplier->id }}"
                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                            >
                                {{ $supplier->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('supplier_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Purchase Date --}}
                <div class="col-md-6">

                    <label
                        for="purchase_date"
                        class="form-label"
                    >
                        Purchase Date
                    </label>

                    <input
                        type="date"
                        name="purchase_date"
                        id="purchase_date"
                        class="form-control @error('purchase_date') is-invalid @enderror"
                        value="{{ old('purchase_date', date('Y-m-d')) }}"
                        required
                    >

                    @error('purchase_date')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- Purchase Items --}}
    <div class="card mb-3">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>Purchase Items</strong>

            <button
                type="button"
                class="btn btn-sm btn-primary"
                id="addItemBtn"
            >
                + Add Medicine
            </button>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    class="table table-sm table-bordered align-middle"
                    id="purchaseItemsTable"
                >

                    <thead>

                        <tr>

                            <th style="min-width: 180px;">
                                Medicine
                            </th>

                            <th>
                                Batch No.
                            </th>

                            <th>
                                Manufactured
                            </th>

                            <th>
                                Expiry
                            </th>

                            <th style="width: 100px;">
                                Quantity
                            </th>

                            <th style="width: 130px;">
                                Purchase Price
                            </th>

                            <th style="width: 130px;">
                                Selling Price
                            </th>

                            <th style="width: 130px;">
                                Subtotal
                            </th>

                            <th style="width: 60px;">
                                #
                            </th>

                        </tr>

                    </thead>


                    <tbody id="purchaseItemsBody">

                        {{-- Items will be added here by JavaScript --}}

                    </tbody>


                    <tfoot>

                        <tr>

                            <th colspan="7" class="text-end">
                                Total:
                            </th>

                            <th>
                                <span id="grandTotal">
                                    TZS 0.00
                                </span>
                            </th>

                            <th></th>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>


    {{-- Submit --}}
    <div class="text-end">

        <button
            type="submit"
            class="btn btn-success"
            id="savePurchaseBtn"
        >
            Save Purchase
        </button>

    </div>

</form>


</div>

{{-- Medicine Data --}}

<script>

    const medicines = @json($medicines);

</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const addItemBtn = document.getElementById('addItemBtn');

    const purchaseItemsBody =
        document.getElementById('purchaseItemsBody');

    const grandTotal =
        document.getElementById('grandTotal');


    let itemIndex = 0;


    /*
    |--------------------------------------------------------------------------
    | Add Purchase Item
    |--------------------------------------------------------------------------
    */

    function addPurchaseItem() {

        const row = document.createElement('tr');

        row.innerHTML = `

            <td>

                <select
                    name="items[${itemIndex}][medicine_id]"
                    class="form-select medicine-select"
                    required
                >

                    <option value="">
                        Select Medicine
                    </option>

                    ${medicines.map(medicine => `

                        <option value="${medicine.id}">
                            ${medicine.name}
                            ${medicine.strength ? ' - ' + medicine.strength : ''}
                        </option>

                    `).join('')}

                </select>

            </td>


            <td>

                <input
                    type="text"
                    name="items[${itemIndex}][batch_number]"
                    class="form-control"
                    required
                >

            </td>


            <td>

                <input
                    type="date"
                    name="items[${itemIndex}][manufactured_at]"
                    class="form-control"
                >

            </td>


            <td>

                <input
                    type="date"
                    name="items[${itemIndex}][expires_at]"
                    class="form-control"
                    required
                >

            </td>


            <td>

                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    class="form-control quantity"
                    min="1"
                    step="1"
                    required
                >

            </td>


            <td>

                <input
                    type="number"
                    name="items[${itemIndex}][purchase_price]"
                    class="form-control purchase-price"
                    min="0"
                    step="0.01"
                    required
                >

            </td>


            <td>

                <input
                    type="number"
                    name="items[${itemIndex}][selling_price]"
                    class="form-control selling-price"
                    min="0"
                    step="0.01"
                    required
                >

            </td>


            <td>

                <input
                    type="text"
                    class="form-control subtotal"
                    value="0.00"
                    readonly
                >

            </td>


            <td>

                <button
                    type="button"
                    class="btn btn-sm btn-danger remove-item"
                >
                    ×
                </button>

            </td>

        `;


        purchaseItemsBody.appendChild(row);

        itemIndex++;

    }


    /*
    |--------------------------------------------------------------------------
    | Calculate Totals
    |--------------------------------------------------------------------------
    */

    function calculateTotals() {

        let total = 0;


        document
            .querySelectorAll('#purchaseItemsBody tr')
            .forEach(row => {

                const quantity =
                    parseFloat(
                        row.querySelector('.quantity')?.value
                    ) || 0;


                const purchasePrice =
                    parseFloat(
                        row.querySelector('.purchase-price')?.value
                    ) || 0;


                const subtotal =
                    quantity * purchasePrice;


                const subtotalInput =
                    row.querySelector('.subtotal');


                if (subtotalInput) {

                    subtotalInput.value =
                        subtotal.toFixed(2);

                }


                total += subtotal;

            });


        grandTotal.textContent =
            'TZS ' +
            total.toLocaleString(
                'en-US',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Add Item
    |--------------------------------------------------------------------------
    */

    addItemBtn.addEventListener(
        'click',
        addPurchaseItem
    );


    /*
    |--------------------------------------------------------------------------
    | Calculate when quantity or price changes
    |--------------------------------------------------------------------------
    */

    purchaseItemsBody.addEventListener(
        'input',
        function (event) {

            if (
                event.target.classList.contains('quantity') ||
                event.target.classList.contains('purchase-price')
            ) {

                calculateTotals();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Remove Item
    |--------------------------------------------------------------------------
    */

    purchaseItemsBody.addEventListener(
        'click',
        function (event) {

            if (
                event.target.classList.contains('remove-item')
            ) {

                event.target
                    .closest('tr')
                    .remove();

                calculateTotals();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Add First Row Automatically
    |--------------------------------------------------------------------------
    */

    addPurchaseItem();


});

</script>

@endsection
