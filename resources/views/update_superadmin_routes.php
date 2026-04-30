<?php
$dir = 'c:\\laragon\\www\\blud_system\\resources\\views';

$replacements = [
    "route('users.index')" => "route('admin.users.index')",
    "route('users.pelanggan')" => "route('admin.users.pelanggan')",
    "route('transaksi.index')" => "route('admin.transaksi.index')",
    "route('transaksi.show')" => "route('admin.transaksi.show')",
    "route('dashboard.monitoring')" => "route('admin.dashboard.monitoring')",
    "route('dashboard.rekap-keuangan')" => "route('admin.dashboard.rekap-keuangan')",
    "route('dashboard.pembagian-pendapatan')" => "route('admin.dashboard.pembagian-pendapatan')",
    "request()->routeIs('users.*')" => "request()->routeIs('admin.users.*')",
    "request()->routeIs('dashboard')" => "request()->routeIs('admin.dashboard')",
];

function replaceInDir($dir, $replacements) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            replaceInDir($path, $replacements);
        } else if (str_ends_with($file, '.blade.php')) {
            $content = file_get_contents($path);
            $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
            
            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                echo "Updated: $path\n";
            }
        }
    }
}

replaceInDir($dir, $replacements);
echo "Done.\n";
