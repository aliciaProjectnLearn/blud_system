<?php
$dir = 'c:\\laragon\\www\\blud_system\\resources\\views';

$replacements = [
    "route('adminfutsal." => "route('admin.futsal.",
    "route('adminac."     => "route('admin.ac.",
    "route('adminkantin." => "route('admin.kantin.",
    "route('adminservis." => "route('admin.servis.",
    "request()->routeIs('adminfutsal." => "request()->routeIs('admin.futsal.",
    "request()->routeIs('adminac."     => "request()->routeIs('admin.ac.",
    "request()->routeIs('adminkantin." => "request()->routeIs('admin.kantin.",
    "request()->routeIs('adminservis." => "request()->routeIs('admin.servis.",
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
