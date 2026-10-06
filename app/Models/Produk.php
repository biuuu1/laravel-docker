<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Uang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produk';

    protected $fillable = [
        'kategori_id',
        'sku',
        'nama',
        'harga',
        'aktif',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok' => 'integer',
        'aktif' => 'boolean',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('produk.aktif', true);
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('produk.stok', '>', 0);
    }

    public function scopeKategoriKode(Builder $query, string $kode): Builder
    {
        return $query->whereIn(
            'kategori_id',
            Kategori::query()
                ->where('kode', $kode)
                ->select('id'),
        );
    }

    public function hargaFormat(): string
    {
        return (new Uang($this->harga))->format();
    }
}
