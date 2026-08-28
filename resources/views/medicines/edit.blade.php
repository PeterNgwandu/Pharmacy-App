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
                <h3 class="mt-5">Medicine Edit Form | <span><a class="btn btn-sm btn-success mb-1" href="{{ route('medicines.index') }}">View Medicines Lists</a></span>
                </h3>

                <div class="row">
                    <div class="col col-md-8">
                        <form method="POST" action="{{ route('medicines.update', $medicine) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Medicine Name</label>
                                    <input type="text" id="" name="name" value="{{ old('name', $medicine->name) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Generic Name</label>
                                    <input type="text" id="" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Brand Name</label>
                                    <input type="text" id="" name="brand_name" value="{{ old('brand_name', $medicine->brand_name) }}" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Category Name</label>
                                    <select class="form-control" name="category_id" id="" type="text">
                                        @foreach ($categories as $category)
                                            <option value="{{ old('category_id',$category->id) }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Dosage Form</label>
                                    <select class="form-control" name="dosage_form_id" id="" type="text">
                                        @foreach ($dosage_forms as $dosage_form)
                                            <option value="{{ old('dosage_form_id',$dosage_form->id) }}">{{ $dosage_form->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-3">
                                    <label for="medicine_name" class="col-form-label">Strength</label>
                                    <input type="text" id="" name="strength" value="{{ old('strength', $medicine->strength) }}" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Base Unit</label>
                                    <input type="text" name="base_unit" id="" value="{{ old('base_unit', $medicine->base_unit) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Pack Unit</label>
                                    <input type="text" name="pack_unit" id="" value="{{ old('pack_unit', $medicine->pack_unit) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Units Per Pack</label>
                                    <input type="number" name="units_per_pack" id="" value="{{ old('units_per_pack', $medicine->units_per_pack) }}" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Barcode (Optional)</label>
                                    <input type="text" name="barcode" id="" value="{{ old('barcode', $medicine->barcode) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Reorder Level</label>
                                    <input type="number" name="reorder_level" id="" value="{{ old('reorder_level', $medicine->reorder_level) }}" class="form-control">
                                </div>
                                <div class="col mb-3">
                                    <label for="base_unit" class="col-form-label">Status</label>
                                        <select name="status" class="form-control" id="">
                                            <option value="1"
                                                {{ old('status', $medicine->status) == 1 ? 'selected' : '' }}>
                                                Active
                                            </option>

                                            <option value="0"
                                                {{ old('status', $medicine->status) == 0 ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-sm btn-success">Edit Medicine</button>
                            
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </body>

    <script>
    </script>

</html>