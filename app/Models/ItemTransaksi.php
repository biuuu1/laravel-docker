<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ItemTransaksi extends Model
{
    use HasFactory;

    protected $table = 'item_transaksi';

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function produk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'sku',
        'nama_produk',
        'harga_satuan',
        'kuantitas',
        'diskon',
        'total',
    ];

    protected $casts = [
        'harga_satuan' => 'integer',
        'kuantitas' => 'integer',
        'diskon' => 'integer',
        'total' => 'integer',
    ];

    /** Batasi item berdasarkan transaksi induknya. */
    public function scopeUntukTransaksi(
        \Illuminate\Database\Eloquent\Builder $query,
        int $transaksiId,
    ): \Illuminate\Database\Eloquent\Builder {
        return $query->where('transaksi_id', $transaksiId);
    }
}
