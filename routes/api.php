<?php

use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;

// Rute sederhana
Route::get('/ping', fn () => response()->json(['status' => 'ok']));

// Route parameter wajib
Route::get('/produk/{id}', function (int $id) {
    return response()->json(['produk_id' => $id]);
})->whereNumber('id');

// Route parameter opsional dengan nilai bawaan
Route::get('/kategori/{slug?}', function (?string $slug = 'semua') {
    return response()->json(['kategori' => $slug]);
});

// Route group: prefix, nama, dan middleware bersama
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
});