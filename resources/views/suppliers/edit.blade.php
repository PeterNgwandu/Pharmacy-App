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
               <p>Update Supplier Details</p>
            </h3>

            <div class="row">
                <div class="col col-md-12">

                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        {{-- Supplier Name --}}
                        <div class="col-md-4">
                            <label for="name" class="form-label mt-3">
                                Supplier Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $supplier->name) }}"
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        {{-- Supplier Phone --}}
                        <div class="col-md-4">
                            <label for="phone" class="form-label mt-3">
                                Supplier Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $supplier->phone) }}"
                            >

                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        {{-- Supplier Email --}}
                        <div class="col-md-4">
                            <label for="email" class="form-label mt-3">
                                Supplier Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $supplier->email) }}"
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>


                    <div class="row">

                        {{-- Supplier Address --}}
                        <div class="col-md-4">
                            <label for="address" class="form-label mt-3">
                                Supplier Address
                            </label>

                            <input
                                type="text"
                                name="address"
                                id="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $supplier->address) }}"
                            >

                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        {{-- Status --}}
                        <div class="col-md-4">
                            <label for="status" class="form-label mt-3">
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select @error('status') is-invalid @enderror"
                            >
                                <option value="1"
                                    {{ old('status', $supplier->status) == 1 ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0"
                                    {{ old('status', $supplier->status) == 0 ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        {{-- Update Button --}}
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success mt-3">
                                Update Supplier
                            </button>
                        </div>

                    </div>

                </form>

                </div>
            </div>

        </div>

    </div>


</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.add('hidden');
        }
    });

    window.addEventListener('beforeunload', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.remove('hidden');
        }
    });
</script>

</html>