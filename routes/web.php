<?php

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

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::resource('products', ProductController::class);

Route::get('/pos', [CartController::class, 'index'])->name('pos.index');
Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('cart.remove');

Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'show']);
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
