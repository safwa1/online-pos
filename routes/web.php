<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DelegateController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customer/{id}/{currency}/{startDate}/{endDate}', [CustomerController::class, 'index'])->name('customer.report');
Route::get('/supplier/{id}/{currency}/{startDate}/{endDate}', [SupplierController::class, 'index'])->name('supplier.report');
Route::get('/delegate/{id}/{currency}/{startDate}/{endDate}', [DelegateController::class, 'index'])->name('delegate.report');
Route::get('/customer/pdf/{id}/{currency}/{startDate}/{endDate}', [CustomerController::class, 'downloadAsPdf'])->name('download-as-pdf');
// filament.admin.auth.login

Route::middleware([
    'auth:sanctum',
    'verified'
])->group(function () {

});
