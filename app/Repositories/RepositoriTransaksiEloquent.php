<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;
use App\Models\ItemTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;

final class RepositoriTransaksiEloquent implements RepositoriTransaksi
{
    public function tanggal(string $tanggal): array
    {
        return Transaksi::query()
            ->tanggal($tanggal)
            ->orderBy('nomor')
            ->get()
            ->map($this->keArray(...))
            ->all();
    }

    public function cariNomor(string $nomor): ?array
    {
        $transaksi = Transaksi::query()
            ->where('nomor', $nomor)
            ->first();

        return $transaksi === null ? null : $this->keArray($transaksi);
    }

    public function simpan(array $transaksi): void
    {
        $item = $transaksi['item'];
        unset($transaksi['item']);
        unset($transaksi['total_bayar_format']);

        $baris = Transaksi::create($transaksi);

        $produkIds = Produk::query()
            ->whereIn('sku', array_column($item, 'sku'))
            ->pluck('id', 'sku');

        $sekarang = now();

        $barisItem = array_map(
            static fn (array $i): array => [
                'transaksi_id' => $baris->id,
                'produk_id' => $produkIds[$i['sku']],
                'sku' => $i['sku'],
                'nama_produk' => $i['nama'],
                'harga_satuan' => $i['harga_satuan'],
                'kuantitas' => $i['kuantitas'],
                'diskon' => $i['diskon'],
                'total' => $i['total'],
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ],
            $item,
        );

        ItemTransaksi::insert($barisItem);
    }

    public function perbarui(string $nomor, array $perubahan): void
    {
        Transaksi::query()
            ->where('nomor', $nomor)
            ->update($perubahan);
    }

    public function urutanBerikutnya(string $tanggal): int
    {
        return Transaksi::query()
            ->where(
                'nomor',
                'like',
                'POS-' . str_replace('-', '', $tanggal) . '-%'
            )
            ->count() + 1;
    }

    private function keArray(Transaksi $transaksi): array
    {
        $item = ItemTransaksi::query()
            ->where('transaksi_id', $transaksi->id)
            ->get([
                'sku',
                'nama_produk',
                'harga_satuan',
                'kuantitas',
                'diskon',
                'total',
            ])
            ->map(static fn (ItemTransaksi $i): array => [
                'sku' => $i->sku,
                'nama' => $i->nama_produk,
                'harga_satuan' => $i->harga_satuan,
                'kuantitas' => $i->kuantitas,
                'diskon' => $i->diskon,
                'total' => $i->total,
            ])
            ->all();

        return [
            'nomor' => $transaksi->nomor,
            'waktu' => $transaksi->created_at->toIso8601String(),
            'kasir' => $transaksi->kasir,
            'member' => $transaksi->member,
            'metode_bayar' => $transaksi->metode_bayar->value,
            'metode_label' => $transaksi->metode_bayar->label(),
            'status' => $transaksi->status->value,
            'item' => $item,
            'subtotal' => $transaksi->subtotal,
            'diskon_grosir' => $transaksi->diskon_grosir,
            'diskon_member' => $transaksi->diskon_member,
            'total_diskon' => $transaksi->total_diskon,
            'dpp' => $transaksi->dpp,
            'ppn' => $transaksi->ppn,
            'total' => $transaksi->total,
            'pembulatan' => $transaksi->pembulatan,
            'total_bayar' => $transaksi->total_bayar,
            'dibayar' => $transaksi->dibayar,
            'kembalian' => $transaksi->kembalian,
        ];
    }
}
