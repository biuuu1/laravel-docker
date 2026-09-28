<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;

// Rute publik: dipakai monitoring untuk memastikan layanan hidup.
Route::get('/ping', fn () => response()->json([
    'status' => 'ok',
    'waktu' => now()->toIso8601String(),
]))->name('api.ping');

// API v1 POS. "kasir" dipasang di level grup, jadi berjalan lebih dulu
// daripada middleware level rute (peran, jam.buka, agen).
Route::prefix('v1/pos')
    ->name('api.v1.pos.')
    ->middleware('kasir')
    ->group(function () {

        /* ---------------- Katalog produk ---------------- */
        Route::get('/produk', [ProdukController::class, 'index'])
            ->name('produk.index');

        // TODO: ganti ke controller asli setelah diperiksa
        Route::get('/produk/{sku}', fn (string $sku) => response()->json(['stub' => true, 'sku' => $sku]))
            ->where('sku', 'SKU-[0-9]{3}')
            ->name('produk.show');

        /* ---------------- Transaksi (TODO: ganti ke TransaksiController milik Augusta) ---------------- */
        Route::get('/transaksi', fn () => response()->json(['stub' => true, 'data' => []]))
            ->name('transaksi.index');

        Route::post('/transaksi', fn () => response()->json(['stub' => true], 201))
            ->middleware(['agen', 'jam.buka'])
            ->name('transaksi.store');

        Route::get('/transaksi/{nomor}', fn (string $nomor) => response()->json(['stub' => true, 'nomor' => $nomor]))
            ->where('nomor', 'POS-[0-9]{8}-[0-9]{4}')
            ->name('transaksi.show');

        Route::post('/transaksi/{nomor}/batal', fn (string $nomor) => response()->json(['stub' => true, 'nomor' => $nomor, 'status' => 'batal']))
            ->middleware('peran:supervisor')
            ->where('nomor', 'POS-[0-9]{8}-[0-9]{4}')
            ->name('transaksi.batal');

        /* ---------------- Laporan (TODO: ganti ke LaporanController) ---------------- */
        Route::prefix('laporan')->name('laporan.')->group(function () {
            // {tanggal?} = parameter opsional
            Route::get('/harian/{tanggal?}', fn (?string $tanggal = null) => response()->json(['stub' => true, 'tanggal' => $tanggal ?? now()->toDateString()]))
                ->where('tanggal', '[0-9]{4}-[0-9]{2}-[0-9]{2}')
                ->name('harian');

            Route::get('/terlaris', fn () => response()->json(['stub' => true, 'data' => []]))
                ->name('terlaris');
        });
    });
