<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duplicates = DB::table('users')
    ->select('no_hp', DB::raw('count(*) as count'))
    ->groupBy('no_hp')
    ->having('count', '>', 1)
    ->get();

foreach ($duplicates as $dup) {
    echo "Found duplicate: {$dup->no_hp} ({$dup->count} times)\n";
    if ($dup->no_hp) {
        $users = User::where('no_hp', $dup->no_hp)->get();
        foreach ($users as $index => $user) {
            if ($index > 0) {
                $newNoHp = $user->no_hp . '_' . time() . '_' . $index;
                echo "Updating user ID {$user->id} from {$user->no_hp} to {$newNoHp}\n";
                $user->update(['no_hp' => $newNoHp]);
            }
        }
    }
}
