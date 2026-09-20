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
                                            <span id="paymentTotal">0.00</span>
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

```html
<script>

    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        let cart = [];


        /*
        |--------------------------------------------------------------------------
        | Current Sale Total
        |--------------------------------------------------------------------------
        */

        let currentTotal = 0;


        /*
        |--------------------------------------------------------------------------
        | Cart Elements
        |--------------------------------------------------------------------------
        */

        const medicineSelect =
            document.getElementById('medicine_select');

        const quantityInput =
            document.getElementById('medicine_quantity');

        const addMedicineButton =
            document.getElementById('addMedicineButton');

        const cartBody =
            document.getElementById('cartBody');

        const cartItemCount =
            document.getElementById('cartItemCount');

        const grandTotal =
            document.getElementById('grandTotal');

        const paymentTotal =
            document.getElementById('paymentTotal');


        /*
        |--------------------------------------------------------------------------
        | Payment Elements
        |--------------------------------------------------------------------------
        */

        const paymentMethod =
            document.getElementById('payment_method');

        const paymentAmount =
            document.getElementById('payment_amount');

        const changeAmount =
            document.getElementById('changeAmount');

        const salesForm =
            document.getElementById('salesForm');

        const completeSaleButton =
            document.getElementById('completeSaleButton');


        /*
        |--------------------------------------------------------------------------
        | Format Money
        |--------------------------------------------------------------------------
        */

        function formatMoney(value) {

            return Number(value || 0).toLocaleString('en-US', {

                minimumFractionDigits: 0,

                maximumFractionDigits: 0

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Add Medicine To Cart
        |--------------------------------------------------------------------------
        */

        addMedicineButton.addEventListener('click', function () {

            const medicineId =
                medicineSelect.value;

            const selectedOption =
                medicineSelect.options[
                    medicineSelect.selectedIndex
                ];

            const quantity =
                parseInt(quantityInput.value);


            /*
            |----------------------------------------------------------------------
            | Validate Medicine
            |----------------------------------------------------------------------
            */

            if (!medicineId) {

                alert('Please select a medicine.');

                medicineSelect.focus();

                return;
            }


            /*
            |----------------------------------------------------------------------
            | Validate Quantity
            |----------------------------------------------------------------------
            */

            if (!quantity || quantity < 1) {

                alert('Please enter a valid quantity.');

                quantityInput.focus();

                return;
            }


            /*
            |----------------------------------------------------------------------
            | Check If Medicine Already Exists
            |----------------------------------------------------------------------
            */

            const existingItem =
                cart.find(function (item) {

                    return item.medicine_id == medicineId;

                });


            /*
            |----------------------------------------------------------------------
            | Increase Existing Quantity
            |----------------------------------------------------------------------
            */

            if (existingItem) {

                existingItem.quantity += quantity;

            }


            /*
            |----------------------------------------------------------------------
            | Add New Medicine
            |----------------------------------------------------------------------
            */

            else {

                cart.push({

                    medicine_id: medicineId,

                    name:
                        selectedOption.textContent.trim(),

                    pack_unit:
                        selectedOption.dataset.packUnit ?? '',

                    quantity: quantity,

                    unit_price: 0,

                    subtotal: 0

                });

            }


            /*
            |----------------------------------------------------------------------
            | Render Cart
            |----------------------------------------------------------------------
            */

            renderCart();


            /*
            |----------------------------------------------------------------------
            | Reset Medicine Inputs
            |----------------------------------------------------------------------
            */

            medicineSelect.value = '';

            quantityInput.value = 1;

            medicineSelect.focus();

        });


        /*
        |--------------------------------------------------------------------------
        | Render Cart
        |--------------------------------------------------------------------------
        */

        function renderCart() {

            cartBody.innerHTML = '';


            /*
            |----------------------------------------------------------------------
            | Empty Cart
            |----------------------------------------------------------------------
            */

            if (cart.length === 0) {

                cartBody.innerHTML = `

                    <tr id="emptyCartRow">

                        <td colspan="7"
                            class="text-center text-muted py-4">

                            No medicines added to the sale.

                        </td>

                    </tr>

                `;


                cartItemCount.textContent =
                    '0 Items';


                updateGrandTotal(0);

                generateHiddenInputs();

                return;
            }


            /*
            |----------------------------------------------------------------------
            | Display Cart Items
            |----------------------------------------------------------------------
            */

            cart.forEach(function (item, index) {

                const row =
                    document.createElement('tr');


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

                        ${
                            item.unit_price > 0

                            ? formatMoney(item.unit_price)

                            : '<span class="text-muted">Checking...</span>'
                        }

                    </td>

                    <td>

                        ${
                            item.subtotal > 0

                            ? formatMoney(item.subtotal)

                            : '<span class="text-muted">Checking...</span>'
                        }

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


            /*
            |----------------------------------------------------------------------
            | Cart Count
            |----------------------------------------------------------------------
            */

            cartItemCount.textContent =

                cart.length +

                (
                    cart.length === 1
                        ? ' Item'
                        : ' Items'
                );


            /*
            |----------------------------------------------------------------------
            | Generate Hidden Form Inputs
            |----------------------------------------------------------------------
            */

            generateHiddenInputs();


            /*
            |----------------------------------------------------------------------
            | Attach Cart Events
            |----------------------------------------------------------------------
            */

            attachCartEvents();


            /*
            |----------------------------------------------------------------------
            | Calculate FEFO Preview
            |----------------------------------------------------------------------
            */

            calculatePreview();

        }


        /*
        |--------------------------------------------------------------------------
        | Generate Hidden Cart Inputs
        |--------------------------------------------------------------------------
        |
        | The cart exists in JavaScript only.
        |
        | Laravel cannot receive JavaScript variables directly when the form
        | submits.
        |
        | Therefore we create:
        |
        | items[0][medicine_id]
        | items[0][quantity]
        |
        | items[1][medicine_id]
        | items[1][quantity]
        |
        | etc.
        |
        */

        function generateHiddenInputs() {

            /*
            |----------------------------------------------------------------------
            | Remove Previous Hidden Inputs
            |----------------------------------------------------------------------
            */

            document
                .querySelectorAll('.cart-hidden-input')
                .forEach(function (input) {

                    input.remove();

                });


            /*
            |----------------------------------------------------------------------
            | Create Inputs For Current Cart
            |----------------------------------------------------------------------
            */

            cart.forEach(function (item, index) {

                /*
                |------------------------------------------------------------------
                | Medicine ID
                |------------------------------------------------------------------
                */

                const medicineInput =
                    document.createElement('input');

                medicineInput.type = 'hidden';

                medicineInput.name =
                    `items[${index}][medicine_id]`;

                medicineInput.value =
                    item.medicine_id;

                medicineInput.classList.add(
                    'cart-hidden-input'
                );


                /*
                |------------------------------------------------------------------
                | Quantity
                |------------------------------------------------------------------
                */

                const quantityInput =
                    document.createElement('input');

                quantityInput.type = 'hidden';

                quantityInput.name =
                    `items[${index}][quantity]`;

                quantityInput.value =
                    item.quantity;

                quantityInput.classList.add(
                    'cart-hidden-input'
                );


                /*
                |------------------------------------------------------------------
                | Add To Form
                |------------------------------------------------------------------
                */

                salesForm.appendChild(
                    medicineInput
                );

                salesForm.appendChild(
                    quantityInput
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Attach Cart Events
        |--------------------------------------------------------------------------
        */

        function attachCartEvents() {


            /*
            |----------------------------------------------------------------------
            | Quantity Change
            |----------------------------------------------------------------------
            */

            document
                .querySelectorAll('.cart-quantity')
                .forEach(function (input) {

                    input.addEventListener(
                        'change',
                        function () {

                            const index =
                                parseInt(
                                    this.dataset.index
                                );

                            const quantity =
                                parseInt(
                                    this.value
                                );


                            /*
                            |------------------------------------------------------------------
                            | Validate Quantity
                            |------------------------------------------------------------------
                            */

                            if (
                                !quantity ||
                                quantity < 1
                            ) {

                                this.value =
                                    cart[index].quantity;

                                return;
                            }


                            /*
                            |------------------------------------------------------------------
                            | Update Cart Quantity
                            |------------------------------------------------------------------
                            */

                            cart[index].quantity =
                                quantity;


                            /*
                            |------------------------------------------------------------------
                            | Re-render Cart
                            |------------------------------------------------------------------
                            */

                            renderCart();

                        }
                    );

                });


            /*
            |----------------------------------------------------------------------
            | Remove Item
            |----------------------------------------------------------------------
            */

            document
                .querySelectorAll('.remove-item')
                .forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const index =
                                parseInt(
                                    this.dataset.index
                                );


                            /*
                            |------------------------------------------------------------------
                            | Remove Medicine
                            |------------------------------------------------------------------
                            */

                            cart.splice(
                                index,
                                1
                            );


                            /*
                            |------------------------------------------------------------------
                            | Re-render Cart
                            |------------------------------------------------------------------
                            */

                            renderCart();

                        }
                    );

                });

        }


        /*
        |--------------------------------------------------------------------------
        | FEFO Preview
        |--------------------------------------------------------------------------
        */

        function calculatePreview() {


            /*
            |----------------------------------------------------------------------
            | No Items
            |----------------------------------------------------------------------
            */

            if (cart.length === 0) {

                updateGrandTotal(0);

                return;
            }


            /*
            |----------------------------------------------------------------------
            | Prepare Cart Data
            |----------------------------------------------------------------------
            */

            const items =
                cart.map(function (item) {

                    return {

                        medicine_id:
                            item.medicine_id,

                        quantity:
                            item.quantity

                    };

                });


            /*
            |----------------------------------------------------------------------
            | Send Cart To Laravel
            |----------------------------------------------------------------------
            */

            fetch(
                "{{ route('sales.preview') }}",
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute(
                                    'content'
                                )

                    },

                    body:
                        JSON.stringify({

                            items: items

                        })

                }
            )


            /*
            |----------------------------------------------------------------------
            | Read Response
            |----------------------------------------------------------------------
            */

            .then(function (response) {

                if (!response.ok) {

                    throw new Error(
                        'Preview request failed.'
                    );

                }

                return response.json();

            })


            /*
            |----------------------------------------------------------------------
            | Process Response
            |----------------------------------------------------------------------
            */

            .then(function (data) {


                /*
                |------------------------------------------------------------------
                | Preview Failed
                |------------------------------------------------------------------
                */

                if (!data.success) {

                    alert(

                        data.message ||

                        'Unable to calculate FEFO.'

                    );


                    /*
                    |--------------------------------------------------------------
                    | Reset Prices
                    |--------------------------------------------------------------
                    */

                    cart.forEach(
                        function (item) {

                            item.unit_price =
                                0;

                            item.subtotal =
                                0;

                        }
                    );


                    updateGrandTotal(0);

                    renderCartWithoutPreview();

                    return;
                }


                /*
                |------------------------------------------------------------------
                | Update Cart Prices
                |------------------------------------------------------------------
                */

                if (data.items) {

                    data.items.forEach(
                        function (
                            previewItem,
                            index
                        ) {

                            if (cart[index]) {

                                cart[index].unit_price =
                                    Number(
                                        previewItem.unit_price
                                    ) || 0;

                                cart[index].subtotal =
                                    Number(
                                        previewItem.subtotal
                                    ) || 0;

                            }

                        }
                    );

                }


                /*
                |------------------------------------------------------------------
                | Update Grand Total
                |------------------------------------------------------------------
                */

                if (
                    data.grand_total !==
                    undefined
                ) {

                    updateGrandTotal(
                        data.grand_total
                    );

                }


                /*
                |------------------------------------------------------------------
                | Render Cart Without Preview
                |------------------------------------------------------------------
                */

                renderCartWithoutPreview();


                /*
                |------------------------------------------------------------------
                | Make Sure Hidden Inputs Are Current
                |------------------------------------------------------------------
                */

                generateHiddenInputs();


                /*
                |------------------------------------------------------------------
                | Recalculate Change
                |------------------------------------------------------------------
                */

                calculateChange();

            })


            /*
            |----------------------------------------------------------------------
            | Handle Error
            |----------------------------------------------------------------------
            */

            .catch(function (error) {

                console.error(
                    'FEFO Preview Error:',
                    error
                );


                updateGrandTotal(0);


                alert(
                    'Unable to calculate the sale. Please try again.'
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Render Cart Without Calling Preview Again
        |--------------------------------------------------------------------------
        */

        function renderCartWithoutPreview() {

            cartBody.innerHTML = '';


            /*
            |----------------------------------------------------------------------
            | Empty Cart
            |----------------------------------------------------------------------
            */

            if (cart.length === 0) {

                cartBody.innerHTML = `

                    <tr id="emptyCartRow">

                        <td colspan="7"
                            class="text-center text-muted py-4">

                            No medicines added to the sale.

                        </td>

                    </tr>

                `;


                cartItemCount.textContent =
                    '0 Items';


                generateHiddenInputs();

                return;
            }


            /*
            |----------------------------------------------------------------------
            | Display Items
            |----------------------------------------------------------------------
            */

            cart.forEach(function (item, index) {

                const row =
                    document.createElement('tr');


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

                        ${
                            item.unit_price > 0

                            ? formatMoney(
                                item.unit_price
                            )

                            : '0'
                        }

                    </td>

                    <td>

                        ${
                            item.subtotal > 0

                            ? formatMoney(
                                item.subtotal
                            )

                            : '0'
                        }

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


            /*
            |----------------------------------------------------------------------
            | Cart Count
            |----------------------------------------------------------------------
            */

            cartItemCount.textContent =

                cart.length +

                (
                    cart.length === 1
                        ? ' Item'
                        : ' Items'
                );


            /*
            |----------------------------------------------------------------------
            | Reattach Events
            |----------------------------------------------------------------------
            */

            attachCartEvents();


            /*
            |----------------------------------------------------------------------
            | Keep Hidden Inputs Updated
            |----------------------------------------------------------------------
            */

            generateHiddenInputs();

        }


        /*
        |--------------------------------------------------------------------------
        | Update Grand Total
        |--------------------------------------------------------------------------
        */

        function updateGrandTotal(total) {

            /*
            |----------------------------------------------------------------------
            | Store Actual Numeric Total
            |----------------------------------------------------------------------
            */

            currentTotal =
                Number(total) || 0;


            /*
            |----------------------------------------------------------------------
            | Format For Display
            |----------------------------------------------------------------------
            */

            const formattedTotal =
                formatMoney(currentTotal);


            /*
            |----------------------------------------------------------------------
            | Update Cart Total
            |----------------------------------------------------------------------
            */

            grandTotal.textContent =
                formattedTotal;


            /*
            |----------------------------------------------------------------------
            | Update Payment Total
            |----------------------------------------------------------------------
            */

            paymentTotal.textContent =
                formattedTotal;


            /*
            |----------------------------------------------------------------------
            | Recalculate Change
            |----------------------------------------------------------------------
            */

            calculateChange();

        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Change
        |--------------------------------------------------------------------------
        */

        function calculateChange() {

            const total =
                currentTotal;


            const paid =
                parseFloat(
                    paymentAmount.value
                ) || 0;


            /*
            |----------------------------------------------------------------------
            | CASH
            |----------------------------------------------------------------------
            */

            if (
                paymentMethod.value ===
                'CASH'
            ) {

                const change =
                    paid - total;


                if (change >= 0) {

                    changeAmount.textContent =
                        formatMoney(change);

                }

                else {

                    changeAmount.textContent =
                        '0';

                }

            }


            /*
            |----------------------------------------------------------------------
            | ELECTRONIC PAYMENT
            |----------------------------------------------------------------------
            */

            else {

                changeAmount.textContent =
                    '0';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Payment Amount Changed
        |--------------------------------------------------------------------------
        */

        paymentAmount.addEventListener(
            'input',
            function () {

                calculateChange();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Payment Method Changed
        |--------------------------------------------------------------------------
        */

        paymentMethod.addEventListener(
            'change',
            function () {

                calculateChange();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Payment Before Sale
        |--------------------------------------------------------------------------
        */

        salesForm.addEventListener(
            'submit',
            function (event) {

                const total =
                    currentTotal;


                const paid =
                    parseFloat(
                        paymentAmount.value
                    ) || 0;


                const method =
                    paymentMethod.value;


                /*
                |------------------------------------------------------------------
                | Make Absolutely Sure Hidden Cart Inputs Exist
                |------------------------------------------------------------------
                */

                generateHiddenInputs();


                /*
                |------------------------------------------------------------------
                | Validate Cart
                |------------------------------------------------------------------
                */

                if (cart.length === 0) {

                    event.preventDefault();

                    alert(
                        'Please add at least one medicine to the sale.'
                    );

                    return;
                }


                /*
                |------------------------------------------------------------------
                | Validate Total
                |------------------------------------------------------------------
                */

                if (total <= 0) {

                    event.preventDefault();

                    alert(
                        'The sale total is not available. Please wait for the FEFO preview to complete.'
                    );

                    return;
                }


                /*
                |------------------------------------------------------------------
                | Validate Payment Method
                |------------------------------------------------------------------
                */

                if (!method) {

                    event.preventDefault();

                    alert(
                        'Please select a payment method.'
                    );

                    paymentMethod.focus();

                    return;
                }


                /*
                |------------------------------------------------------------------
                | Validate Amount
                |------------------------------------------------------------------
                */

                if (paid <= 0) {

                    event.preventDefault();

                    alert(
                        'Please enter the amount paid.'
                    );

                    paymentAmount.focus();

                    return;
                }


                /*
                |------------------------------------------------------------------
                | CASH PAYMENT
                |------------------------------------------------------------------
                */

                if (method === 'CASH') {

                    if (paid < total) {

                        event.preventDefault();

                        alert(
                            'Amount paid cannot be less than the sale total.'
                        );

                        paymentAmount.focus();

                        return;
                    }

                }


                /*
                |------------------------------------------------------------------
                | ELECTRONIC PAYMENT
                |------------------------------------------------------------------
                */

                else {

                    if (
                        paid.toFixed(2) !==
                        total.toFixed(2)
                    ) {

                        event.preventDefault();

                        alert(
                            'For ' +
                            method.replace('_', ' ') +
                            ', the amount paid must exactly equal the sale total.'
                        );

                        paymentAmount.focus();

                        return;
                    }

                }


                /*
                |------------------------------------------------------------------
                | Prevent Double Submission
                |------------------------------------------------------------------
                */

                if (completeSaleButton) {

                    completeSaleButton.disabled =
                        true;

                    completeSaleButton.innerHTML =
                        'Processing Sale...';

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Initial Cart Render
        |--------------------------------------------------------------------------
        */

        renderCart();


        /*
        |--------------------------------------------------------------------------
        | Initial Payment Calculation
        |--------------------------------------------------------------------------
        */

        calculateChange();

    });

</script>
```


</html>