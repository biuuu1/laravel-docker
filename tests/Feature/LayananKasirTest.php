<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\PembayaranKurang;
use App\Exceptions\StokTidakCukup;
use App\Exceptions\TransaksiSudahDibatalkan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\LayananKasir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LayananKasirTest extends TestCase
{
    use RefreshDatabase;

    private function buatProduk(string $sku = 'SKU-TEST-001', int $stok = 10): Produk
    {
        $kategori = Kategori::query()->create([
            'kode' => 'makanan',
            'nama' => 'Makanan',
            'aktif' => true,
        ]);

        $produk = Produk::query()->create([
            'kategori_id' => $kategori->id,
            'sku' => $sku,
            'nama' => 'Produk Tes',
            'harga' => 10000,
            'aktif' => true,
        ]);

        $produk->forceFill(['stok' => $stok])->save();

        return $produk;
    }

    public function test_transaksi_selesai_mengurangi_stok_dan_menyimpan_item(): void
    {
        $produk = $this->buatProduk();

        $this->assertDatabaseHas('produk', [
            'sku' => $produk->sku,
            'stok' => 10,
        ]);
        $this->assertSame(10, (int) Produk::query()
            ->where('sku', $produk->sku)
            ->value('stok'));

        $layanan = app(LayananKasir::class);

        $hasil = $layanan->proses([
            'item' => [
                ['sku' => $produk->sku, 'kuantitas' => 2],
            ],
            'metode_bayar' => 'qris',
        ], 'Kasir Tes');

        $this->assertSame('selesai', $hasil['status']);
        $this->assertCount(1, $hasil['item']);
        $this->assertSame(8, $produk->fresh()->stok);

        $this->assertDatabaseHas('transaksi', [
            'nomor' => $hasil['nomor'],
            'kasir' => 'Kasir Tes',
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('item_transaksi', [
            'transaksi_id' => Transaksi::query()
                ->where('nomor', $hasil['nomor'])
                ->value('id'),
            'sku' => $produk->sku,
            'kuantitas' => 2,
        ]);
    }

    public function test_sku_duplikat_digabung_sebelum_stok_dikurangi(): void
    {
        $produk = $this->buatProduk();
        $layanan = app(LayananKasir::class);

        $hasil = $layanan->proses([
            'item' => [
                ['sku' => $produk->sku, 'kuantitas' => 2],
                ['sku' => ' sku-test-001 ', 'kuantitas' => 3],
            ],
            'metode_bayar' => 'qris',
        ], 'Kasir Tes');

        $this->assertSame(5, $produk->fresh()->stok);
        $this->assertCount(1, $hasil['item']);
        $this->assertSame(5, (int) $hasil['item'][0]['kuantitas']);

        $this->assertDatabaseHas('item_transaksi', [
            'transaksi_id' => Transaksi::query()
                ->where('nomor', $hasil['nomor'])
                ->value('id'),
            'sku' => $produk->sku,
            'kuantitas' => 5,
        ]);
    }

    public function test_sku_duplikat_yang_totalnya_melebihi_stok_ditolak(): void
    {
        $produk = $this->buatProduk(stok: 10);
        $layanan = app(LayananKasir::class);

        try {
            $layanan->proses([
                'item' => [
                    ['sku' => $produk->sku, 'kuantitas' => 6],
                    ['sku' => ' sku-test-001 ', 'kuantitas' => 6],
                ],
                'metode_bayar' => 'qris',
            ], 'Kasir Tes');

            $this->fail('Checkout seharusnya ditolak karena stok tidak cukup.');
        } catch (StokTidakCukup $exception) {
            $this->assertSame('stok_tidak_cukup', $exception->kodeKesalahan());
            $this->assertSame(12, $exception->konteks()['diminta']);
            $this->assertSame(10, $exception->konteks()['tersedia']);
        }

        $this->assertSame(10, $produk->fresh()->stok);
        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_pembatalan_kedua_kali_ditolak_tanpa_mengubah_stok(): void
    {
        $produk = $this->buatProduk();
        $layanan = app(LayananKasir::class);

        $hasil = $layanan->proses([
            'item' => [
                ['sku' => $produk->sku, 'kuantitas' => 3],
            ],
            'metode_bayar' => 'qris',
        ], 'Kasir Tes');

        $layanan->batalkan(
            $hasil['nomor'],
            'Tes pembatalan',
            'Supervisor Tes',
        );

        $this->assertSame(10, $produk->fresh()->stok);

        try {
            $layanan->batalkan(
                $hasil['nomor'],
                'Pembatalan ulang',
                'Supervisor Tes',
            );

            $this->fail('Pembatalan kedua seharusnya ditolak.');
        } catch (TransaksiSudahDibatalkan $exception) {
            $this->assertSame(
                'transaksi_sudah_dibatalkan',
                $exception->kodeKesalahan(),
            );
        }

        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_pembatalan_mengembalikan_stok(): void
    {
        $produk = $this->buatProduk();
        $layanan = app(LayananKasir::class);

        $hasil = $layanan->proses([
            'item' => [
                ['sku' => $produk->sku, 'kuantitas' => 3],
            ],
            'metode_bayar' => 'qris',
        ], 'Kasir Tes');

        $this->assertSame(7, $produk->fresh()->stok);

        $batal = $layanan->batalkan(
            $hasil['nomor'],
            'Tes pembatalan',
            'Supervisor Tes',
        );

        $this->assertSame('batal', $batal['status']);
        $this->assertSame(10, $produk->fresh()->stok);

        $this->assertDatabaseHas('transaksi', [
            'nomor' => $hasil['nomor'],
            'status' => 'batal',
            'alasan_batal' => 'Tes pembatalan',
            'dibatalkan_oleh' => 'Supervisor Tes',
        ]);
    }

    public function test_pembayaran_kurang_ditolak_tanpa_menyimpan_transaksi_atau_mengurangi_stok(): void
    {
        $produk = $this->buatProduk(stok: 10);
        $layanan = app(LayananKasir::class);

        try {
            $layanan->proses([
                'item' => [
                    ['sku' => $produk->sku, 'kuantitas' => 2],
                ],
                'metode_bayar' => 'tunai',
                'dibayar' => 1000,
            ], 'Kasir Tes');

            $this->fail('Pembayaran kurang seharusnya ditolak.');
        } catch (PembayaranKurang $exception) {
            $this->assertSame('pembayaran_kurang', $exception->kodeKesalahan());
            $this->assertGreaterThan(0, $exception->konteks()['kurang']);
        }

        $this->assertSame(10, (int) $produk->fresh()->stok);
        $this->assertSame(0, Transaksi::query()->count());
        $this->assertDatabaseCount('item_transaksi', 0);
    }
}
