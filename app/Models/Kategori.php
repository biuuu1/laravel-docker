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
        'aktif' => 'boolean',
    ];

    /** Menjembatani baris tabel dengan enum domain. */
    public function enum(): EnumKategori
    {
        return EnumKategori::from($this->kode);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('kategori.aktif', true);
    }
}
