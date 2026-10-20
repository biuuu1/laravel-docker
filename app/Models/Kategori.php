<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Kategori as EnumKategori;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'kode',
        'nama',
        'aktif',
    ];

    protected $casts = [
        'kode' => EnumKategori::class,
        'aktif' => 'boolean',
    ];

    public function produk(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }


    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('kategori.aktif', true);
    }
}
