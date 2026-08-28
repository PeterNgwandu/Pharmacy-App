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
                <h3 class="mt-5">Receive Medicine Form | <span><a class="btn btn-sm btn-success mb-1" href="{{ route('medicines_batch.index') }}">View Medicines Lists</a></span>
                </h3>

                <div class="row">
                    <div class="col col-md-6">
                        <form method="POST" action="{{ route('medicines_batch.store') }}">
                            @csrf

                            <div class="row">

                                {{-- Medicine --}}
                                <div class="col-md-6 mb-3">
                                    <label for="medicine_id" class="form-label">Medicine</label>

                                    <select name="medicine_id" id="medicine_id" class="form-select" required>
                                        <option value="">-- Select Medicine --</option>

                                        @foreach ($medicines as $medicine)
                                            <option value="{{ $medicine->id }}"
                                                data-pack-unit="{{ $medicine->pack_unit }}"
                                                data-base-unit="{{ $medicine->base_unit }}"
                                                data-units-per-pack="{{ $medicine->units_per_pack }}"
                                                {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                                {{ $medicine->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('medicine_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Batch Number --}}
                                <div class="col-md-6 mb-3">
                                    <label for="batch_number" class="form-label">Batch Number</label>

                                    <input
                                        type="text"
                                        id="batch_number"
                                        name="batch_number"
                                        value="Auto Generated"
                                        class="form-control"
                                        readonly
                                    >

                                    @error('batch_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>


                            <div class="row">

                                {{-- Manufactured Date --}}
                                <div class="col-md-6 mb-3">
                                    <label for="manufactured_at" class="form-label">
                                        Manufactured Date
                                    </label>

                                    <input
                                        type="date"
                                        id="manufactured_at"
                                        name="manufactured_at"
                                        value="{{ old('manufactured_at') }}"
                                        class="form-control"
                                    >

                                    @error('manufactured_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Expiry Date --}}
                                <div class="col-md-6 mb-3">
                                    <label for="expires_at" class="form-label">
                                        Expiry Date
                                    </label>

                                    <input
                                        type="date"
                                        id="expires_at"
                                        name="expires_at"
                                        value="{{ old('expires_at') }}"
                                        class="form-control"
                                        required
                                    >

                                    @error('expires_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>


                            <div class="row">

                                {{-- Purchase Price --}}
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_price" class="form-label">
                                        Purchase Price
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="purchase_price"
                                        id="purchase_price"
                                        value="{{ old('purchase_price') }}"
                                        class="form-control"
                                    >

                                    @error('purchase_price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Selling Price --}}
                                <div class="col-md-6 mb-3">
                                    <label for="selling_price" class="form-label">
                                        Selling Price
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="selling_price"
                                        id="selling_price"
                                        value="{{ old('selling_price') }}"
                                        class="form-control"
                                    >

                                    @error('selling_price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                

                            </div>


                            <div class="row">

                                {{-- Quantity --}}
                                <div class="col-md-4 mb-3">
                                    <label for="quantity" class="form-label">
                                        Quantity
                                    </label>

                                    <input
                                        type="number"
                                        min="1"
                                        name="quantity"
                                        id="quantity"
                                        value="{{ old('quantity') }}"
                                        class="form-control"
                                        required
                                    >

                                    <small class="text-muted">
                                        Enter quantity in {{ $medicine->pack_unit ?? 'pack units' }}
                                    </small>

                                    @error('quantity')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">

                                    <label for="quantity_received" class="form-label">
                                         Quantity Received (<span id="pack_unit_label">Pack</span>)
                                    </label>

                                    <input
                                        type="text"
                                        id="quantity_received"
                                        class="form-control"
                                        readonly
                                    >

                                    <small id="pack_info" class="text-muted"></small>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Base Unit Quantity
                                    </label>

                                    <input
                                        type="text"
                                        id="base_quantity"
                                        class="form-control"
                                        readonly
                                    >

                                </div>

                                {{-- Status --}}
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">
                                        Status
                                    </label>

                                    <select name="status" id="status" class="form-select" required>
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>

                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>


                            <div class="mt-3">
                                <button type="submit" class="btn btn-sm btn-success">
                                    Receive Medicine
                                </button>

                                <a href="{{ route('medicines_batch.create') }}"
                                class="btn btn-sm btn-secondary">
                                    Cancel
                                </a>
                            </div>
                            
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </body>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#usersTable', {
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>

    <script>

        const medicineSelect = document.getElementById('medicine_id');
        const quantityInput = document.getElementById('quantity');
        const quantityReceived = document.getElementById('quantity_received');
        const baseQuantityInput = document.getElementById('base_quantity');
        const packInfo = document.getElementById('pack_info');


        function calculateQuantity() {

            const selectedOption =
                medicineSelect.options[medicineSelect.selectedIndex];

            // Nothing selected
            if (!selectedOption || !selectedOption.value) {

                quantityReceived.value = '';
                baseQuantityInput.value = '';
                packInfo.textContent = '';

                return;
            }


            const packUnit =
                selectedOption.dataset.packUnit;

            const baseUnit =
                selectedOption.dataset.baseUnit;

            const unitsPerPack =
                parseInt(selectedOption.dataset.unitsPerPack) || 1;

            const quantity =
                parseInt(quantityInput.value) || 0;


            // Display conversion
            packInfo.textContent =
                `1 ${packUnit} = ${unitsPerPack} ${baseUnit}`;


            // Quantity Received
            quantityReceived.value =
                quantity > 0
                    ? `${quantity} ${packUnit}`
                    : '';


            // Base Unit Quantity
            const baseQuantity =
                quantity * unitsPerPack;

            baseQuantityInput.value =
                quantity > 0
                    ? `${baseQuantity} ${baseUnit}`
                    : '';

        }


        medicineSelect.addEventListener(
            'change',
            calculateQuantity
        );


        quantityInput.addEventListener(
            'input',
            calculateQuantity
        );


    </script>

</html>