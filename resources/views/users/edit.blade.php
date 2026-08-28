<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
            <div class="card text-bg-light">
                <div class="card-header"> <h2>Edit User</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}" >
                        @csrf
                        @method('PUT')
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Username -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="username" value="{{ old('username', $user->username) }}" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Select User Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />

                            <select name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role}}" 
                                        {{ old('role', $userRole) == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                                            

                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            
                            <button type="submit" class="btn btn-dark mt-4">Update User</button>
                        </div>
                    </form>
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

</html>