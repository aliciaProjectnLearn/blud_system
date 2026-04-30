<?php

use App\Models\SewaRuko;
use App\Models\Penyewa;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

SewaRuko::whereNull('penyewa_id')
    ->whereNotNull('user_id')
    ->get()
    ->each(function($s) {
        $p = Penyewa::firstOrCreate(
            ['user_id' => $s->user_id],
            [
                'nama_usaha' => $s->nama_penyewa,
                'alamat' => '-'
            ]
        );
        $s->update(['penyewa_id' => $p->id]);
        echo "Linked Sewa {$s->id} to Penyewa {$p->id}\n";
    });
