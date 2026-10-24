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
               <p>Add Supplier To The System | <span class="text-sm text-muted">Create Supplier</span> </p>
            </h3>

            <div class="row">
                <div class="col col-md-12">

                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col col-md-4">
                                <label for="supplier_name" class="form-label mt-3">Supplier Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>

                            <div class="col col-md-4">
                                <label for="supplier_phone" class="form-label mt-3">Supplier Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>

                            <div class="col col-md-4">
                                <label for="supplier_email" class="form-label mt-3">Supplier Email</label>
                                <input type="text" name="email" id="email" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-md-4">
                                <label for="supplier_address" class="form-label mt-3">Supplier Address</label>
                                <input type="text" name="address" id="naaddressme" class="form-control">
                            </div>

                            <input type="hidden" name="status" id="status" class="form-control">
                            <input type="hidden" name="created_by" id="status" class="form-control">

                            <div class="col col-md-4">
                                <button type="submit" class="mt-12 btn btn-success">Create Supplier</button>
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