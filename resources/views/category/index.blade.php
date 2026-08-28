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
            
            <div class="container-fluid">
            <div class="row">
                <!--- Div for Category --->
                <div class="col col-md-6">
                    <h3 class="mt-5">Categories List 
                        <span>&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Create Category</button>
                        </span>
                    </h3>

                    <!--- Create Category Form --->
                    <div>
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Category</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('categories.create') }}">
                                        @csrf
                                    <div class="mb-3">
                                        <label for="category_name" class="col-form-label">Category Name</label>
                                        <input type="text" class="form-control" id="category_name" name="name" value="name">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Create</button>
                                    </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--- Edit Category Form --->
                    <div>
                        <div class="modal fade" id="editCategoryForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Existing Category</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" id="editCategoryFormAction" action="">
                                        @csrf
                                        @method('PUT')
                                    <div class="mb-3">
                                        <label for="category_name" class="col-form-label">Category Name</label>
                                        <input type="text" class="form-control" id="editCategoryName" name="name" value="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <table id="usersTable" class="table table-sm table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                {{-- <th scope="col">Category ID</th> --}}
                                <th scope="col">Name</th>
                                <th scope="col">Date Created</th>
                                <th scope="col">Date Updated</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    {{-- <td>{{ $category->id }}</td> --}}
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $category->updated_at }}</td>
                                    <td>
                                        <a href="#" 
                                            id="edit-category-{{ $category->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryForm"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            <i class="bi bi-pen-fill text-warning"></i>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" 
                                            id="delete-form-{{ $category->id }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="" onclick="confirmDelete({{ $category->id }})">
                                                <i class="bi bi-trash3 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>

                <!-- Dosage Form -->
                <div class="col col-md-6">
                    <h3 class="mt-5">DosageForm List 
                        <span>&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#dosage">Create DosageForm</button>
                        </span>
                    </h3>

                    <!--- Create Dosage Form --->
                    <div>
                        <div class="modal fade" id="dosage" tabindex="-1" aria-labelledby="dosageFormModal" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="dosageFormModal">Create Dosage Form</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('dosage_forms.store') }}">
                                        @csrf
                                    <div class="mb-3">
                                        <label for="category_name" class="col-form-label">DosageForm Name</label>
                                        <input type="text" class="form-control" id="category_name" name="name" value="name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_name" class="col-form-label">DosageForm Description</label>
                                        <textarea type="text" class="form-control" id="category_name" name="description" value="description"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Create</button>
                                    </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--- Edit Dosage Form --->
                    <div>
                        <div class="modal fade" id="editDosageForm" tabindex="-1" aria-labelledby="dosageFormModal" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Existing Dosage Form</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" id="editDosageFormsAction" action="">
                                        @csrf
                                        @method('PUT')
                                    <div class="mb-3">
                                        <label for="dosageFormName" class="col-form-label">Dosage Form Name</label>
                                        <input type="text" class="form-control" id="editDosageFormName" name="name" value="name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="dosageFormDescription" class="col-form-label">Dosage Form Description</label>
                                        <textarea type="text" class="form-control" id="editDosageFormDescription" name="description"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <table id="dosageFormTable" class="table table-sm table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                {{-- <th scope="col">Category ID</th> --}}
                                <th scope="col">Name</th>
                                <th scope="col">Date Created</th>
                                <th scope="col">Date Updated</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dosage_forms as $dosage_form)
                                <tr>
                                    {{-- <td>{{ $dosage_form->id }}</td> --}}
                                    <td>{{ $dosage_form->name }}</td>
                                    <td>{{ $dosage_form->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $dosage_form->updated_at }}</td>
                                    <td>
                                        <a href="#" 
                                            id="edit-dosageForm-{{ $dosage_form->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editDosageForm"
                                            data-id="{{ $dosage_form->id }}"
                                            data-name="{{ $dosage_form->name }}"
                                            data-description="{{ $dosage_form->description }}">
                                            <i class="bi bi-pen-fill text-warning"></i>
                                        </a>
                                        <form method="POST" action="{{ route('dosage_forms.destroy', $dosage_form) }}" 
                                            id="delete-dosageForm-{{ $dosage_form->id }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="" onclick="confirmDeleteDosageForm({{ $dosage_form->id }})">
                                                <i class="bi bi-trash3 text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>

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

        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#dosageFormTable', {
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });

        // Edit Categories
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[id^="edit-category-"]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    document.getElementById('editCategoryName').value = name;
                    document.getElementById('editCategoryFormAction').action =
                        `/categories/${id}`;
                });
            });
        });

        // Edit Dosage Forms
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[id^="edit-dosageForm-"]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const description = this.dataset.description;

                    document.getElementById('editDosageFormName').value = name;
                    document.getElementById('editDosageFormDescription').value = description;
                    document.getElementById('editDosageFormsAction').action =
                        `/dosage_forms/${id}`;
                });
            });
        });


    </script>

</html>