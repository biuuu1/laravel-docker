<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ItemTransaksi>
 */
final class ItemTransaksiFactory extends Factory
{
    public function definition(): array
    {
        $kuantitas = $this->faker->numberBetween(1, 5);
        $harga = $this->faker->numberBetween(20, 500) * 100;

        return [
            'transaksi_id' => Transaksi::factory(),
            'produk_id' => Produk::factory(),
            'sku' => 'SKU-'.$this->faker->unique()->numberBetween(100, 999),
            'nama_produk' => ucwords($this->faker->words(3, true)),
            'harga_satuan' => $harga,
            'kuantitas' => $kuantitas,
            'diskon' => 0,
            'total' => $harga * $kuantitas,
        ];
    }

    /** Item dengan kuantitas yang memenuhi batas grosir. */
    public function grosir(): static
    {
        return $this->state(function (array $attributes): array {
            $kuantitas = 12;
            $harga = (int) $attributes['harga_satuan'];
            $diskon = (int) round(
                $harga * $kuantitas * (float) config('pos.grosir.persen', 5) / 100
            );

            return [
                'kuantitas' => $kuantitas,
                'diskon' => $diskon,
                'total' => ($harga * $kuantitas) - $diskon,
            ];
        });
    }

    /** Item normal tanpa diskon, untuk kasus transaksi reguler. */
    public function tanpaDiskon(): static
    {
        return $this->state(fn (): array => ['diskon' => 0]);
    }
}
