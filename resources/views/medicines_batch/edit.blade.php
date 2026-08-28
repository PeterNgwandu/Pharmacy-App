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
                <h3 class="mt-5">Medicine Receive Edit Form 
                </h3>

                <div class="row">
                    <div class="col col-md-8">
                           <form action="{{ route('medicines_batch.update', $medicineBatch) }}" method="POST">
                            @csrf
                            @method('PUT')
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="medicine_id" class="form-label">Medicine</label>
                                        <select name="medicine_id" class="form-select" id="medicine_id">
                                            <option value="">Selecte Medicine</option>
                                            @foreach ($medicines as $medicine)
                                                <option value="{{ $medicine->id }}" {{ old('medicine_id', $medicineBatch->medicine->id) == $medicine->id ? 'selected' : '' }}>{{ $medicine->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('medicine_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="batch_number" class="form-label">Batch Number</label>
                                        <input type="text" id="batch_number" name="batch_number" value="{{ $medicineBatch->batch_number }}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="manufactured_at" class="form-label">Manufactured</label>
                                        <input
                                            type="date"
                                            id="manufactured_at"
                                            name="manufactured_at"
                                            class="form-control"
                                            value="{{ old('manufactured_at', $medicineBatch->manufactured_at?->format('Y-m-d')) }}"
                                        >
                                        @error('manufactured_at')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="expires_at" class="form-label">Expiry</label>
                                        <input
                                            type="date"
                                            id="expires_at"
                                            name="expires_at"
                                            class="form-control"
                                            value="{{ old('expires_at', $medicineBatch->expires_at?->format('Y-m-d')) }}"
                                        >
                                        @error('manufactured_at')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="purchase_price" class="form-label">Purchase Price</label>
                                        <input
                                            type="number"
                                            id="purchase_price"
                                            name="purchase_price"
                                            class="form-control"
                                            value="{{ old('purchase_price', $medicineBatch->purchase_price) }}"
                                        >
                                        @error('purchase_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="selling_price" class="form-label">Selling Price</label>
                                        <input
                                            type="number"
                                            id="selling_price"
                                            name="selling_price"
                                            class="form-control"
                                            value="{{ old('selling_price', $medicineBatch->selling_price) }}"
                                        >
                                        @error('selling_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input
                                            type="number"
                                            id="quantity"
                                            name="quantity"
                                            class="form-control"
                                            value="{{ old('quantity', (int) $medicineBatch->quantity) }}"
                                            min="1"
                                            step="1"
                                            readonly
                                        >
                                        <small id="quantity" class="text-muted">Use Stock Adjustment to change quantity.</small>
                                        @error('quantity')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select" required>

                                            <option value="1"
                                                {{ old('status', $medicineBatch->status) == 1 ? 'selected' : '' }}>
                                                Active
                                            </option>

                                            <option value="0"
                                                {{ old('status', $medicineBatch->status) == 0 ? 'selected' : '' }}>
                                                Inactive
                                            </option>

                                        </select>
                                                                            @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Edit Medicine Batch</button>
                           </form>
                    </div>
                </div>

            </div>

        </div>

    </body>

    <script>
    </script>

</html>