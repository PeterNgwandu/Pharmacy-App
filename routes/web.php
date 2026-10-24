<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DosageFormController;
use App\Http\Controllers\MedicineBatchController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierAccountController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalesController;
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

// Sales / POS
Route::get('pos/sales', [SalesController::class, 'index'])->name('sales.index');
Route::get('pos/sales/create', [SalesController::class, 'create'])->name('sales.create');
Route::post('pos/sales/', [SalesController::class, 'store'])->name('sales.store');
Route::get('pos/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
Route::post('pos/sales/preview', [SalesController::class, 'preview'])->name('sales.preview');
Route::get('pos/sales/{sale}/print', [SalesController::class, 'print'])->name('sales.print');
Route::get('/sales/{sale}/download-pdf', [SalesController::class, 'downloadPdf'])->name('sales.downloadPdf');

// Suppliers
Route::get('system/supplier', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('system/supplier/create', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('system/suppliers/listing', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('system/suppliers/edit/{supplier}', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::get('system/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
Route::put('system/suppliers/update/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('system/suppliers/delete/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

// Purchases
Route::get('system/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
Route::get('system/purchase/create', [PurchaseController::class, 'create'])->name('purchases.create');
Route::get('system/purchases/purchase/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
Route::post('system/purchases/purchase/store', [PurchaseController::class, 'store'])->name('purchases.store');
Route::post('system/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

// Supplier purchases payments
Route::post('system/purchases/{purchase}/payments', [SupplierPaymentController::class, 'store'])->name('supplier_payments.store');

// Supplier Accounts
Route::get('system/supplier-accounts', [SupplierAccountController::class, 'index'])->name('supplier_accounts.index');

require __DIR__.'/auth.php';
