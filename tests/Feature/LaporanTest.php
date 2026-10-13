<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ItemTransaksi;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\LayananLaporan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function buatTransaksi(
        string $nomor,
        string $status,
        string $metode,
        int $totalBayar,
        int $diskon,
        int $ppn,
        string $waktu,
    ): Transaksi {
        $transaksi = Transaksi::query()->create([
            'nomor' => $nomor,
            'kasir' => 'Kasir Tes',
            'member' => false,
            'metode_bayar' => $metode,
            'status' => $status,
            'subtotal' => $totalBayar + $diskon,
            'diskon_grosir' => 0,
            'diskon_member' => $diskon,
            'total_diskon' => $diskon,
            'dpp' => $totalBayar - $ppn,
            'ppn' => $ppn,
            'total' => $totalBayar,
            'pembulatan' => 0,
            'total_bayar' => $totalBayar,
            'dibayar' => $totalBayar,
            'kembalian' => 0,
        ]);

        $transaksi->forceFill([
            'created_at' => Carbon::parse($waktu),
            'updated_at' => Carbon::parse($waktu),
        ])->save();

        return $transaksi;
    }

    private function tambahItem(
        Transaksi $transaksi,
        string $sku,
        string $nama,
        int $kuantitas,
        int $harga,
    ): void {
        $kategori = Kategori::query()->firstOrCreate(
            ['kode' => 'makanan'],
            ['nama' => 'Kategori Tes Laporan', 'aktif' => true],
        );

        $produk = Produk::query()->firstOrCreate(
            ['sku' => $sku],
            [
                'kategori_id' => $kategori->id,
                'nama' => $nama,
                'harga' => $harga,
                'stok' => 100,
                'aktif' => true,
            ],
        );

        ItemTransaksi::query()->create([
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'sku' => $sku,
            'nama_produk' => $nama,
            'harga_satuan' => $harga,
            'kuantitas' => $kuantitas,
            'diskon' => 0,
            'total' => $kuantitas * $harga,
        ]);
    }

    public function test_laporan_harian_hanya_menghitung_transaksi_selesai(): void
    {
        $tanggal = '2026-10-10';

        $tunai = $this->buatTransaksi(
            'POS-20261010-0001',
            'selesai',
            'tunai',
            11000,
            1000,
            1000,
            "$tanggal 09:00:00",
        );

        $this->buatTransaksi(
            'POS-20261010-0002',
            'selesai',
            'qris',
            20000,
            0,
            2000,
            "$tanggal 10:00:00",
        );

        $this->buatTransaksi(
            'POS-20261010-0003',
            'batal',
            'tunai',
            50000,
            0,
            5000,
            "$tanggal 11:00:00",
        );

        $hasil = app(LayananLaporan::class)->harian($tanggal);

        $this->assertSame($tanggal, $hasil['tanggal']);
        $this->assertSame(2, $hasil['jumlah_transaksi']);
        $this->assertSame(31000, $hasil['omzet']);
        $this->assertSame(1000, $hasil['total_diskon']);
        $this->assertSame(3000, $hasil['total_ppn']);
        $this->assertSame(15500, $hasil['rata_rata_struk']);
        $perMetode = $hasil['per_metode_bayar'];
        ksort($perMetode);

        $this->assertSame([
            'qris' => 20000,
            'tunai' => 11000,
        ], $perMetode);
    }

    public function test_laporan_harian_kosong_mengembalikan_nilai_nol(): void
    {
        $hasil = app(LayananLaporan::class)->harian('2026-10-11');

        $this->assertSame(0, $hasil['jumlah_transaksi']);
        $this->assertSame(0, $hasil['omzet']);
        $this->assertSame(0, $hasil['total_diskon']);
        $this->assertSame(0, $hasil['total_ppn']);
        $this->assertSame(0, $hasil['rata_rata_struk']);
        $this->assertSame([], $hasil['per_metode_bayar']);
    }

    public function test_produk_terlaris_diurutkan_berdasarkan_kuantitas_dan_mengecualikan_transaksi_batal(): void
    {
        $tanggal = '2026-10-10';

        $pertama = $this->buatTransaksi(
            'POS-20261010-0011',
            'selesai',
            'qris',
            30000,
            0,
            3000,
            "$tanggal 09:00:00",
        );

        $kedua = $this->buatTransaksi(
            'POS-20261010-0012',
            'selesai',
            'tunai',
            20000,
            0,
            2000,
            "$tanggal 10:00:00",
        );

        $batal = $this->buatTransaksi(
            'POS-20261010-0013',
            'batal',
            'qris',
            90000,
            0,
            9000,
            "$tanggal 11:00:00",
        );

        $this->tambahItem($pertama, 'SKU-101', 'Beras', 3, 10000);
        $this->tambahItem($pertama, 'SKU-102', 'Gula', 2, 5000);
        $this->tambahItem($kedua, 'SKU-101', 'Beras', 2, 10000);
        $this->tambahItem($batal, 'SKU-103', 'Minyak', 9, 10000);

        $hasil = app(LayananLaporan::class)->terlaris($tanggal, 5);

        $this->assertCount(2, $hasil);
        $this->assertSame('SKU-101', $hasil[0]['sku']);
        $this->assertSame('Beras', $hasil[0]['nama']);
        $this->assertSame(5, $hasil[0]['kuantitas']);
        $this->assertSame(50000, $hasil[0]['pendapatan']);
        $this->assertSame('SKU-102', $hasil[1]['sku']);
        $this->assertSame(2, $hasil[1]['kuantitas']);
    }
}
