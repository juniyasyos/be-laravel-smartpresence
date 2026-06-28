<?php

$file = __DIR__ . '/database/seeders/SmartPresenceSeeder.php';
$content = file_get_contents($file);

$content = preg_replace("/\], 'slug'=>Illuminate\\\\Support\\\\Str::slug\('([^']+)'\]\),/", ",'slug'=>Illuminate\Support\Str::slug('$1')],", $content);

file_put_contents($file, $content);
echo "Fixed!";
