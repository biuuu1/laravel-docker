<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_menghasilkan_minimal_lima_puluh_baris(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $kategori = \App\Models\Kategori::query()->count();
        $produk = \App\Models\Produk::query()->count();
        $transaksi = \App\Models\Transaksi::query()->count();
        $item = \App\Models\ItemTransaksi::query()->count();

        $total = $kategori + $produk + $transaksi + $item;

        $this->assertSame(4, $kategori);
        $this->assertSame(36, $produk);
        $this->assertSame(3, $transaksi);
        $this->assertSame(7, $item);
        $this->assertGreaterThanOrEqual(50, $total);
    }

    public function test_database_seeder_dijalankan_dua_kali_tanpa_menggandakan_data(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $sebelum = [
            \App\Models\Kategori::query()->count(),
            \App\Models\Produk::query()->count(),
            \App\Models\Transaksi::query()->count(),
            \App\Models\ItemTransaksi::query()->count(),
        ];

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $sesudah = [
            \App\Models\Kategori::query()->count(),
            \App\Models\Produk::query()->count(),
            \App\Models\Transaksi::query()->count(),
            \App\Models\ItemTransaksi::query()->count(),
        ];

        $this->assertSame($sebelum, $sesudah);
    }
}
