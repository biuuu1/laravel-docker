# Studi Kasus POS — Modul 4

## 1. Ruang lingkup

Dokumentasi ini mencatat implementasi migrasi, seeder, factory, model Eloquent,
CRUD API, dan aturan bisnis aplikasi Point of Sale (POS).

## 2. Kondisi data contoh

Data pada database utama yang telah diperiksa:

| Data | Jumlah/kondisi |
|---|---:|
| Kategori | 4 |
| Produk | 30 |
| Produk aktif/tersedia | 27 |
| Transaksi | 3 |
| Transaksi selesai | 2 |
| Transaksi batal | 1 |
| Item transaksi | 7 |
| Stok SKU-006 | 468 |
| Stok SKU-009 | 200 |

Seeder telah diuji dua kali pada database SQLite sementara. Jumlah data dan
stok contoh tetap konsisten setelah pengujian ulang. Database utama tidak
digunakan untuk pengujian rollback atau reset.

## 3. Migrasi

Empat migrasi inti POS:

- `create_kategori_table`
- `create_produk_table`
- `create_transaksi_table`
- `create_item_transaksi_table`

Status terakhir yang diperiksa: seluruh migrasi berstatus `Ran`.
Pengujian migrate dan rollback maju-mundur terisolasi masih perlu dicatat
sebagai bukti tersendiri.

## 4. Model dan relasi

- `Kategori` memiliki banyak `Produk`.
- `Produk` dimiliki satu `Kategori` dan memiliki banyak `ItemTransaksi`.
- `Transaksi` memiliki banyak `ItemTransaksi`.
- `ItemTransaksi` terhubung ke `Transaksi` dan `Produk`.
- Produk memakai soft delete.
- Item transaksi menyimpan snapshot SKU, nama produk, dan harga satuan.

## 5. Aturan bisnis

### AB-11 — stok transaksi

Penyelesaian transaksi mengurangi stok produk. Pembatalan transaksi
mengembalikan stok. Operasi transaksi harus bersifat atomik sehingga kegagalan
tidak meninggalkan perubahan stok atau item yang setengah tersimpan.

### AB-12 — snapshot item

Setiap item transaksi menyimpan SKU, nama produk, dan harga satuan saat
transaksi dibuat. Data historis tersebut tidak bergantung pada perubahan
informasi produk setelah transaksi.

### Stok pada CRUD produk

Endpoint CRUD produk tidak menerima perubahan stok dari payload pengguna.
Stok dikelola oleh alur transaksi.

## 6. API yang terdaftar

Semua endpoint berikut menggunakan prefix `/api/v1/pos`.

| Metode | Endpoint | Kegunaan |
|---|---|---|
| GET | `/produk` | Daftar produk |
| POST | `/produk` | Tambah produk |
| GET | `/produk/{sku}` | Detail produk |
| PUT | `/produk/{sku}` | Ubah informasi produk |
| DELETE | `/produk/{sku}` | Soft delete produk |
| GET | `/produk-terhapus` | Daftar produk terhapus |
| POST | `/produk/{sku}/restore` | Pulihkan produk |
| GET | `/transaksi` | Daftar transaksi |
| POST | `/transaksi` | Buat transaksi |
| GET | `/transaksi/{nomor}` | Detail transaksi |
| POST | `/transaksi/{nomor}/batal` | Batalkan transaksi |
| GET | `/laporan/harian` | Laporan harian |
| GET | `/laporan/terlaris` | Laporan produk terlaris |

Route produk dan transaksi telah diperiksa menggunakan `artisan route:list`.
Endpoint produk memerlukan API key; operasi pengelolaan produk dan pembatalan
transaksi dibatasi menurut peran supervisor sesuai middleware route.

## 7. Hasil pengujian yang sudah dijalankan

- `ProdukCrudTest`: 4 tes lulus, 19 assertions.
- `LayananKasirTest`: sebelumnya 2 tes lulus, mencakup penyelesaian transaksi
  dan pengembalian stok saat pembatalan.
- Pengujian seeder dua kali dilakukan pada SQLite sementara.

## 8. Bukti yang masih perlu dilengkapi

- Bukti migrate dan rollback seluruh empat migrasi pada database sementara.
- Screenshot `migrate:status`.
- Pengujian CRUD penuh untuk minimal dua entitas.
- Koleksi Postman v2.1 minimal 10 request beserta assertion aturan bisnis.
- Environment Postman tanpa menyertakan rahasia API.
- Hasil Collection Runner atau Newman tanpa kegagalan.
- Catatan konflik merge sebelum dan sesudah penyelesaian.
- Bukti pull request, review, merge, dan penghapusan branch lama.
