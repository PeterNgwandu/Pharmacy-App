<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DosageFormController;
use App\Http\Controllers\MedicineBatchController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Manage users routes
Route::get('/users/index', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Manage Medicines, Categories and Dosage Forms  route
Route::get('/medicines/index', [MedicineController::class, 'index'])->name('medicines.index');
Route::get('/medicines/create', [MedicineController::class, 'create'])->name('medicines.create');
Route::get('/medicines/{medicine}/edit', [MedicineController::class, 'edit'])->name('medicines.edit');
Route::put('/medicines/{medicine}', [MedicineController::class, 'update'])->name('medicines.update');
Route::post('/medicines/store', [MedicineController::class, 'store'])->name('medicines.store');
Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy'])->name('medicines.destroy');

Route::get('/categories/index', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.create');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::post('/dosage_forms/store', [DosageFormController::class, 'store'])->name('dosage_forms.store');
Route::put('/dosage_forms/{dosageForm}', [DosageFormController::class, 'update'])->name('dosage_forms.update');
Route::delete('/dosage_forms/{dosageForm}', [DosageFormController::class, 'destroy'])->name('dosage_forms.destroy');

// Manage Batch
Route::get('/medicine/batch/index', [MedicineBatchController::class, 'index'])->name('medicines_batch.index');
Route::get('/medicine/batch/create', [MedicineBatchController::class, 'create'])->name('medicines_batch.create');
Route::get('/medicine/batch/{medicineBatch}/edit', [MedicineBatchController::class, 'edit'])->name('medicines_batch.edit');
Route::put('/medicine/batch/{medicineBatch}/update', [MedicineBatchController::class, 'update'])->name('medicines_batch.update');
Route::post('/medicine/batch/store', [MedicineBatchController::class, 'store'])->name('medicines_batch.store');
Route::delete('/medicine/batch/{medicineBatch}', [MedicineBatchController::class, 'destroy'])->name('medicines_batch.destroy');

// Stock Movement
Route::get('medicine/stock-movements/listings', [StockMovementController::class, 'index'])->name('stock_movement.listings');
Route::get('medicine/stock-movements/adjustment', [StockMovementController::class, 'createAdjustment'])->name('stock_movement.adjustment');
Route::post('/medicine/stock-movement/adjustment', [StockMovementController::class, 'adjustmentStore'])->name('stock_movement.adjustmentStore');

// FEFO
Route::get('/medicine/batches/fefo', [MedicineBatchController::class, 'testFefo'])->name('medicine_batches.fefo');

require __DIR__.'/auth.php';
