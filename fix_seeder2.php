<?php

$file = __DIR__ . '/database/seeders/SmartPresenceSeeder.php';
$content = file_get_contents($file);

$content = str_replace("Illuminate\Support\Str::slug", "\Illuminate\Support\Str::slug", $content);

file_put_contents($file, $content);
echo "Fixed slashes!";
