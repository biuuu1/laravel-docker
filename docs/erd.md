# ERD — Sistem POS Modul 4

```mermaid
erDiagram
    KATEGORI ||--o{ PRODUK : memiliki
    PRODUK ||--o{ ITEM_TRANSAKSI : dicatat_dalam
    TRANSAKSI ||--|{ ITEM_TRANSAKSI : terdiri_dari

    KATEGORI {
        bigint id PK
        varchar kode UK
        varchar nama
        boolean aktif
        timestamp created_at
        timestamp updated_at
    }

    PRODUK {
        bigint id PK
        bigint kategori_id FK
        varchar sku UK
        varchar nama
        unsignedInteger harga
        unsignedInteger stok
        boolean aktif
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    TRANSAKSI {
        bigint id PK
        varchar nomor UK
        varchar kasir
        boolean member
        varchar metode_bayar
        varchar status
        unsignedInteger subtotal
        unsignedInteger diskon_grosir
        unsignedInteger diskon_member
        unsignedInteger total_diskon
        unsignedInteger dpp
        unsignedInteger ppn
        unsignedInteger total
        unsignedInteger pembulatan
        unsignedInteger total_bayar
        unsignedInteger dibayar
        unsignedInteger kembalian
        varchar alasan_batal
        varchar dibatalkan_oleh
        timestamp dibatalkan_pada
        timestamp created_at
        timestamp updated_at
    }

    ITEM_TRANSAKSI {
        bigint id PK
        bigint transaksi_id FK
        bigint produk_id FK
        varchar sku
        varchar nama_produk
        unsignedInteger harga_satuan
        unsignedInteger kuantitas
        unsignedInteger diskon
        unsignedInteger total
        timestamp created_at
        timestamp updated_at
    }
```

## Catatan relasi

- Satu kategori memiliki banyak produk.
- Satu transaksi memiliki item transaksi.
- Satu produk dapat tercatat pada banyak item transaksi.
- Item transaksi menyimpan snapshot SKU, nama produk, dan harga satuan agar riwayat transaksi tetap terbaca ketika data produk berubah.
- `created_at`, `updated_at`, dan `deleted_at` ditampilkan sesuai penggunaan timestamps dan soft deletes.
- Relasi foreign key mengikuti aturan penghapusan yang didefinisikan pada migration.
