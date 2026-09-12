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
                <h3 class="mt-5">Stock Adjustments
                </h3>

                <form action="{{ route('stock_movement.adjustmentStore') }}" method="POST">
                    @csrf

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label for="medicine_batch_id" class="form-label"> Medicine</label>
                            <select name="medicine_batch_id" id="medicine_batch_id" class="form-select" required>
                                <option value="">Select Medicine Batch</option>

                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ old('medicine_batch_id') == $batch->id ? 'selected' : '' }}>
                                       {{ $batch->medicine->name }}
                                        -
                                        {{ $batch->batch_number }}
                                        -
                                        Stock Qty: {{ (int) $batch->quantity }} 
                                    </option>
                                @endforeach

                            </select>

                            @error('medicine_batch_id')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Adjustment Type --}}
                        <div class="col-md-2 mb-3">
                            <label for="adjustment_type" class="form-label">Adjustment Type</label>
                            <select name="adjustment_type" id="adjustment_type" class="form-select" required>
                                <option value="">Select Adjustment Type</option>

                                <option value="increase">Increase</option>
                                <option value="decrease">Decrease</option>
                            </select>

                            @error('adjustment_type')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-2 mb-3">

                        {{-- Quantity --}}
                            <label for="quantity" id="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                            min="1"
                            step="1"
                            value="{{ old('quantity') }}"
                            required>

                            @error('quantity')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="row">
                        
                        {{-- Reason --}}
                        <div class="col-md-8 mb-3">

                            <label for="notes" class="form-label">
                                Reason / Notes
                            </label>

                            <textarea
                                name="notes"
                                id="notes"
                                class="form-control"
                                rows="3"
                                required
                            >{{ old('notes') }}</textarea>

                            @error('notes')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                        
                    <button type="submit" class="btn btn-success">Adjust Stock</button>

                </form>

            </div>

        </div>

    </body>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#medicinesBatchTable', {
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>

</html>