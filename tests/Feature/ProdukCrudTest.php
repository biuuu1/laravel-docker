<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProdukCrudTest extends TestCase
{
    use RefreshDatabase;

    private function siapkanKategori(): Kategori
    {
        return Kategori::query()->create([
            'kode' => 'makanan',
            'nama' => 'Makanan',
            'aktif' => true,
        ]);
    }

    private function sebagaiSupervisor(): static
    {
        return $this->withHeader('X-API-Key', (string) config('pos.kasir') === ''
            ? 'spv-dev-001'
            : 'spv-dev-001');
    }

    public function test_kasir_tidak_boleh_membuat_produk(): void
    {
        $kategori = $this->siapkanKategori();

        $this->withHeader('X-API-Key', 'kasir-dev-001')
            ->postJson('/api/v1/pos/produk', [
                'kategori_id' => $kategori->id,
                'sku' => 'SKU-901',
                'nama' => 'Produk Tes',
                'harga' => 10000,
            ])
            ->assertForbidden();
    }

    public function test_supervisor_dapat_membuat_memperbarui_menghapus_dan_memulihkan_produk(): void
    {
        $kategori = $this->siapkanKategori();

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->postJson('/api/v1/pos/produk', [
                'kategori_id' => $kategori->id,
                'sku' => 'SKU-901',
                'nama' => 'Produk Tes',
                'harga' => 10000,
            ])
            ->assertCreated()
            ->assertJsonPath('data.sku', 'SKU-901');

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->putJson('/api/v1/pos/produk/SKU-901', [
                'nama' => 'Produk Tes Baru',
                'harga' => 12000,
            ])
            ->assertOk()
            ->assertJsonPath('data.nama', 'Produk Tes Baru')
            ->assertJsonPath('data.harga', 12000);

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->deleteJson('/api/v1/pos/produk/SKU-901')
            ->assertOk();

        $this->assertSoftDeleted('produk', ['sku' => 'SKU-901']);

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->getJson('/api/v1/pos/produk-terhapus')
            ->assertOk()
            ->assertJsonPath('data.0.sku', 'SKU-901');

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->postJson('/api/v1/pos/produk/SKU-901/restore')
            ->assertOk()
            ->assertJsonPath('data.sku', 'SKU-901');

        $this->assertDatabaseHas('produk', [
            'sku' => 'SKU-901',
            'deleted_at' => null,
        ]);
    }

    public function test_stok_tidak_bisa_diubah_melalui_endpoint_crud(): void
    {
        $kategori = $this->siapkanKategori();

        $produk = Produk::query()->create([
            'kategori_id' => $kategori->id,
            'sku' => 'SKU-903',
            'nama' => 'Produk Stok',
            'harga' => 10000,
            'aktif' => true,
        ]);

        $produk->forceFill(['stok' => 12])->save();

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->putJson('/api/v1/pos/produk/SKU-903', [
                'nama' => 'Produk Stok Diperbarui',
                'stok' => 999,
            ])
            ->assertOk()
            ->assertJsonPath('data.stok', 12);

        $this->assertSame(12, (int) $produk->fresh()->stok);
    }

    public function test_sku_duplikat_ditolak(): void
    {
        $kategori = $this->siapkanKategori();

        Produk::query()->create([
            'kategori_id' => $kategori->id,
            'sku' => 'SKU-902',
            'nama' => 'Produk Awal',
            'harga' => 10000,
            'aktif' => true,
        ]);

        $this->withHeader('X-API-Key', 'spv-dev-001')
            ->postJson('/api/v1/pos/produk', [
                'kategori_id' => $kategori->id,
                'sku' => 'SKU-902',
                'nama' => 'Produk Duplikat',
                'harga' => 15000,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sku');
    }
}
