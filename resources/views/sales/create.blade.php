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
                POS Sale |

                <span>
                    <a class="btn btn-sm btn-success mb-1"
                       href="{{ route('sales.index') }}">
                        View Sales
                    </a>
                </span>
            </h3>


            {{-- Sale Form --}}
            <form action="{{ route('sales.store') }}"
                  method="POST"
                  id="salesForm">

                @csrf


                <div class="row">

                    {{-- ========================= --}}
                    {{-- LEFT SIDE --}}
                    {{-- ========================= --}}
                    <div class="col-md-8">

                        {{-- Add Medicine --}}
                        <div class="card shadow-sm mb-3">

                            <div class="card-header bg-white">
                                <strong>Add Medicine</strong>
                            </div>


                            <div class="card-body">

                                <div class="row align-items-end">

                                    {{-- Medicine --}}
                                    <div class="col-md-8 mb-3">

                                        <label for="medicine_select"
                                               class="form-label">
                                            Medicine
                                        </label>

                                        <select id="medicine_select"
                                                class="form-select">

                                            <option value="">
                                                Select Medicine
                                            </option>

                                            @foreach ($medicines as $medicine)

                                                <option value="{{ $medicine->id }}" data-pack-unit="{{ $medicine->pack_unit }}">

                                                    {{ $medicine->name }}

                                                    @if ($medicine->strength)
                                                        - {{ $medicine->strength }}
                                                    @endif

                                                    ({{ $medicine->pack_unit }})

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    {{-- Quantity --}}
                                    <div class="col-md-2 mb-3">

                                        <label for="medicine_quantity"
                                               class="form-label">
                                            Quantity
                                        </label>

                                        <input type="number"
                                               id="medicine_quantity"
                                               class="form-control"
                                               min="1"
                                               step="1"
                                               value="1">

                                    </div>


                                    {{-- Add Button --}}
                                    <div class="col-md-2 mb-3">

                                        <button type="button"
                                                class="btn btn-success w-100"
                                                id="addMedicineButton">

                                            Add

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ========================= --}}
                        {{-- CURRENT SALE --}}
                        {{-- ========================= --}}
                        <div class="card shadow-sm">

                            <div class="card-header bg-white d-flex justify-content-between align-item-center">
                                <strong>Current Sale Cart</strong>

                                <span class="badge bg-primary text-white p-1" id="cartItemCount">
                                    0 Item
                                </span>

                            </div>


                            <div class="card-body p-0">
                                <div class="table-responsive">

                                    <table class="table table-sm table-bordered table-striped mb-0">

                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>

                                                <th style="width: 35%;">
                                                    Medicine
                                                </th>

                                                <th style="width: 15%;">
                                                    Quantity
                                                </th>

                                                <th style="width: 15%;">
                                                    Unit
                                                </th>

                                                <th style="width: 15%;">
                                                    Price / Pack
                                                </th>

                                                <th style="width: 15%;">
                                                    Subtotal
                                                </th>

                                                <th style="width: 5%;">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="cartBody">
                                            <tr id="emptyCartRow">

                                                <td colspan="7"
                                                    class="text-center text-muted py-4">

                                                    No medicines added to the sale.

                                                </td>

                                            </tr>
                                        </tbody>

                                        <tfoot>

                                             <tr>

                                                <th colspan="5"
                                                    class="text-end">

                                                    Total:

                                                </th>

                                                <th colspan="2">

                                                    <span id="grandTotal">
                                                        0.00
                                                    </span>

                                                </th>

                                            </tr>

                                        </tfoot>
                                        

                                    </table>

                                </div>
                            </div>

                        </div>

                    </div>


                    {{-- ========================= --}}
                    {{-- RIGHT SIDE --}}
                    {{-- ========================= --}}
                    <div class="col-md-4">

                        <div class="card shadow-sm">

                            <div class="card-header bg-white">
                                <strong>Payment</strong>
                            </div>


                            <div class="card-body">

                                {{-- Total Amount --}}
                                <div class="mb-4">

                                    <label for="" class="form-label">Total Amount</label>

                                    <div class="border rounded bg-light p-3">

                                        <h2 class="mb-0 text-end">
                                            <span id="grandTotal">0.00</span>
                                        </h2>

                                    </div>

                                </div>

                                {{-- Payment Method --}}
                                <div class="mb-3">

                                    <label for="payment_method" class="form-label">Payment Method</label>

                                    <select name="payment_method" id="payment_method" class="form-select" required>
                                        <option value="">Select Payment Method</option>

                                        <option value="CASH">CASH</option>
                                        <option value="LIPA_HAPA">LIPA HAPA</option>
                                        <option value="BANK">BANK</option>

                                    </select>

                                    @error('payment_method')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror

                                </div>

                                {{-- Amount Paid --}}
                                <div class="mb-3">

                                    <label for="amount_paid" class="form-label">Amount Paid</label>

                                    <input type="number" class="form-control form-control-lg"
                                        name="amount"
                                        id="payment_amount"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('payment_amount') }}"
                                        required>

                                    @error('payment_amount')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                {{-- Change --}}
                                <div class="mb-3">

                                    <label for="change" class="form-label">Change</label>

                                    <div class="border rounded bg-light p-3">
                                        
                                        <h3 class="mb-0 text-end">
                                            <span id="changeAmount">0.00</span>
                                        </h3>

                                    </div>

                                </div>

                                {{-- Complete Sale --}}
                                <button type="submit" id="completeSaleButton" class="btn btn-success btn-lg w-100">
                                    Complete Sale
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


</body>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        let cart = [];


        // Elements
        const medicineSelect = document.getElementById('medicine_select');
        const quantityInput = document.getElementById('medicine_quantity');
        const addMedicineButton = document.getElementById('addMedicineButton');

        const cartBody = document.getElementById('cartBody');
        const cartItemCount = document.getElementById('cartItemCount');
        const grandTotal = document.getElementById('grandTotal');


        // Add medicine to cart
        addMedicineButton.addEventListener('click', function () {

            const medicineId = medicineSelect.value;

            const selectedOption =
                medicineSelect.options[medicineSelect.selectedIndex];

            const quantity =
                parseInt(quantityInput.value);


            // Validate medicine
            if (!medicineId) {

                alert('Please select a medicine.');

                return;
            }


            // Validate quantity
            if (!quantity || quantity < 1) {

                alert('Please enter a valid quantity.');

                return;
            }


            // Check if medicine already exists in cart
            const existingItem = cart.find(function (item) {

                return item.medicine_id == medicineId;

            });


            if (existingItem) {

                // Increase existing quantity
                existingItem.quantity += quantity;

            } else {

                // Add new medicine
                cart.push({

                    medicine_id: medicineId,

                    name: selectedOption.textContent.trim(),

                    pack_unit: selectedOption.dataset.packUnit ?? '',

                    quantity: quantity,

                    unit_price: 0,

                    subtotal: 0

                });

            }


            // Refresh cart
            renderCart();


            // Reset medicine selection
            medicineSelect.value = '';

            quantityInput.value = 1;

            medicineSelect.focus();

        });


        // Render cart
        function renderCart()
        {

            cartBody.innerHTML = '';


            // Empty cart
            if (cart.length === 0) {

                cartBody.innerHTML = `
                    <tr id="emptyCartRow">

                        <td colspan="7"
                            class="text-center text-muted py-4">

                            No medicines added to the sale.

                        </td>

                    </tr>
                `;

                cartItemCount.textContent = '0 Items';

                grandTotal.textContent = '0.00';

                return;
            }


            // Cart items
            cart.forEach(function (item, index) {

                const row = document.createElement('tr');


                row.innerHTML = `

                    <td>
                        ${index + 1}
                    </td>


                    <td>

                        <strong>
                            ${item.name}
                        </strong>

                    </td>


                    <td>

                        <input type="number"
                            class="form-control form-control-sm cart-quantity"
                            min="1"
                            step="1"
                            value="${item.quantity}"
                            data-index="${index}">

                    </td>


                    <td>
                        ${item.pack_unit}
                    </td>


                    <td>
                        <span class="text-muted">
                            FEFO
                        </span>
                    </td>


                    <td>
                        <span class="text-muted">
                            FEFO
                        </span>
                    </td>


                    <td class="text-center">

                        <button type="button"
                                class="btn btn-sm btn-danger remove-item"
                                data-index="${index}">

                            ×

                        </button>

                    </td>

                `;


                cartBody.appendChild(row);

            });


            // Number of items
            cartItemCount.textContent =
                cart.length + (cart.length === 1 ? ' Item' : ' Items');


            // Total
            grandTotal.textContent = '0.00';


            attachCartEvents();

        }


        // Quantity and remove button events
        function attachCartEvents()
        {

            // Quantity change
            document.querySelectorAll('.cart-quantity')
                .forEach(function (input) {

                    input.addEventListener('change', function () {

                        const index =
                            parseInt(this.dataset.index);

                        const quantity =
                            parseInt(this.value);


                        if (!quantity || quantity < 1) {

                            this.value =
                                cart[index].quantity;

                            return;
                        }


                        cart[index].quantity =
                            quantity;


                        renderCart();

                    });

                });


            // Remove medicine
            document.querySelectorAll('.remove-item')
                .forEach(function (button) {

                    button.addEventListener('click', function () {

                        const index =
                            parseInt(this.dataset.index);


                        cart.splice(index, 1);


                        renderCart();

                    });

                });

        }


        // Initial cart display
        renderCart();

});
</script>

</html>