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
                <h3 class="mt-5">Registered Users List</h3>
                
                <table id="usersTable" class="table table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Registered Username</th>
                            <th scope="col">Role</th>
                            <th scope="col">Action</th>
                            <th scope="col">Block/Unblock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                @if ($user->roles->count())
                                  @foreach ($user->roles as $role)
                                    <span class="badge bg-success">{{ $role->name }}</span>                                      
                                  @endforeach
                                @else
                                    <span class="badge bg-warning">No Role Assigned</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}"><i class="bi bi-pen-fill text-warning"></i></a>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="" onclick="confirmDelete({{ $user->id }})">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                                
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="" id="checkNativeSwitch" switch checked>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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