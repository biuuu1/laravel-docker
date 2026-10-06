<?php

return [
    'nama_toko' => env('POS_NAMA_TOKO', 'Barokah Mart Solo'),
    'ppn_persen' => (float) env('POS_PPN_PERSEN', 11),

    'pembulatan' => (int) env('POS_PEMBULATAN', 100),

    'member' => [
        'persen' => (float) env('POS_DISKON_MEMBER', 3),
    ],

    'grosir' => [
        'minimal_kuantitas' => (int) env('POS_GROSIR_MINIMAL', 12),
        'persen' => (float) env('POS_GROSIR_PERSEN', 5),
    ],

    'happy_hour' => [
        'mulai' => env('POS_HAPPY_MULAI', '15:00'),
        'selesai' => env('POS_HAPPY_SELESAI', '17:00'),
        'persen' => (float) env('POS_HAPPY_PERSEN', 2),
    ],

    'jam' => [
        'buka' => env('POS_JAM_BUKA', '07:00'),
        'tutup' => env('POS_JAM_TUTUP', '22:00'),
    ],

    'log' => [
        'ambang_ms' => (float) env('POS_LOG_AMBANG_MS', 100),
    ],

    /*
     * Daftar kunci API kasir. HANYA UNTUK LATIHAN MODUL 3.
     * Autentikasi sesungguhnya memakai Laravel Sanctum pada modul berikutnya.
     * Jangan pernah menaruh kredensial produksi di repositori.
     */
    'kasir' => [
        env('POS_KUNCI_KASIR', 'kasir-dev-001') => [
            'nama' => 'Kasir Dev',
            'peran' => 'kasir',
        ],
        env('POS_KUNCI_SUPERVISOR', 'spv-dev-001') => [
            'nama' => 'Supervisor Dev',
            'peran' => 'supervisor',
        ],
    ],
];
