<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class KategoriFactory extends Factory
{
    public function definition(): array
    {
        $kategori = $this->faker->randomElement(\App\Domain\Kategori::cases());

        return [
            'kode' => $kategori->value,
            'nama' => $kategori->label(),
            'aktif' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['aktif' => false]);
    }

    /** Kategori aktif yang siap digunakan di katalog. */
    public function aktif(): static
    {
        return $this->state(fn () => ['aktif' => true]);
    }
}
