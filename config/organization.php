<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Struktur Organisasi PT Indo Bismar
    |--------------------------------------------------------------------------
    |
    | Hierarki & Jabatan:
    | - Tingkat Divisi: Manager
    | - Tingkat Subdivisi: Kepala, Staff
    |
    */

    'structure' => [
        'Pendidikan' => [
            'Administrasi',
        ],
        'Operasional' => [
            'Gudang',
            'Service',
            'Retur',
            'Quality Control (QC)',
            'Pengiriman',
            'Input Output',
        ],
        'Audit & Fintax' => [
            'Admin Hutang & Pembiayaan',
            'Admin Penjualan Cabang',
            'Audit Internal',
            'Tax Compliance',
        ],
        'Purchasing' => [
            'Purchasing HP & Barang Project',
            'Purchasing Computer',
            'Purchasing Accessories',
        ],
        'HRD' => [],
        'Marketing' => [],
        'Digital Marketing' => [],
        'Satpam' => [],
        'OB' => [],
    ],
];
