<?php

return [
    'bendahara' => [
        'nama' => env('BENDAHARA_NAMA', 'Bendahara BLUD'),
        'no_hp' => env('BENDAHARA_HP', '6281234567890'),
    ],
    'rekening' => [
        'bank' => env('BANK_NAME', 'BRI'),
        'nomor' => env('BANK_NOMOR', '1234-5678-9012-3456'),
        'atas_nama' => env('BANK_ATAS_NAMA', 'Bu Lena - Admin BLUD'),
    ]
];
