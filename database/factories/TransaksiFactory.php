<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\MetodeBayar;
use App\Domain\StatusTransaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Transaksi>
 */
final class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(10_000, 500_000);
        $ppn = (int) round($subtotal * (float) config('pos.ppn_persen', 11) / 100);
        $total = $subtotal + $ppn;
        $kelipatan = max(1, (int) config('pos.pembulatan', 100));
        $totalBayar = (int) (ceil($total / $kelipatan) * $kelipatan);
        $metode = $this->faker->randomElement(MetodeBayar::cases());
        $dibayar = $metode === MetodeBayar::Tunai
            ? $totalBayar + $this->faker->randomElement([0, 0, 5_000, 10_000])
            : $totalBayar;

        return [
            'nomor' => 'TRX-'.strtoupper($this->faker->unique()->bothify('########')),
            'kasir' => $this->faker->name(),
            'member' => false,
            'metode_bayar' => $metode->value,
            'status' => StatusTransaksi::Selesai->value,
            'subtotal' => $subtotal,
            'diskon_grosir' => 0,
            'diskon_member' => 0,
            'total_diskon' => 0,
            'dpp' => $subtotal,
            'ppn' => $ppn,
            'total' => $total,
            'pembulatan' => $totalBayar - $total,
            'total_bayar' => $totalBayar,
            'dibayar' => $dibayar,
            'kembalian' => $dibayar - $totalBayar,
            'alasan_batal' => null,
            'dibatalkan_oleh' => null,
            'dibatalkan_pada' => null,
        ];
    }

    /** Transaksi selesai dengan pembayaran yang valid. */
    public function selesai(): static
    {
        return $this->state(fn () => [
            'status' => StatusTransaksi::Selesai->value,
            'alasan_batal' => null,
            'dibatalkan_oleh' => null,
            'dibatalkan_pada' => null,
        ]);
    }

    /** Transaksi yang dibatalkan oleh supervisor. */
    public function batal(): static
    {
        return $this->state(fn () => [
            'status' => StatusTransaksi::Batal->value,
            'alasan_batal' => 'Contoh pembatalan transaksi',
            'dibatalkan_oleh' => 'Supervisor Dev',
            'dibatalkan_pada' => now(),
        ]);
    }
}
