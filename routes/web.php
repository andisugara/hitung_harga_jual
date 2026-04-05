<?php

use App\Http\Controllers\MasterVariableController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('products.index'));

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::post('/platforms', [PlatformController::class, 'store'])->name('platforms.store');

Route::get('/master-variables', [MasterVariableController::class, 'index'])->name('master-variables.index');
Route::get('/master-variables/{platform}', [MasterVariableController::class, 'show'])->name('master-variables.show');
Route::get('/master-variables/{platform}/edit', [MasterVariableController::class, 'edit'])->name('master-variables.edit');
Route::put('/master-variables/{platform}', [MasterVariableController::class, 'update'])->name('master-variables.update');
