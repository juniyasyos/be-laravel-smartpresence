<?php

$files = [
    __DIR__ . '/app/Http/Controllers/WorkUnitController.php',
    __DIR__ . '/app/Http/Requests/StoreWorkUnitRequest.php',
    __DIR__ . '/app/Http/Requests/UpdateWorkUnitRequest.php',
    __DIR__ . '/tests/Feature/WorkUnitApiTest.php',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    // Replace array keys and variables that strictly referred to work_unit (name of the department)
    // We only replace 'work_unit' (the column/field name)
    $content = str_replace("'work_unit'", "'unit_name'", $content);
    $content = str_replace('"work_unit"', '"unit_name"', $content);
    
    // In WorkUnitController, there's a where('work_unit', 'like', ...)
    $content = str_replace("->where('work_unit',", "->where('unit_name',", $content);
    
    file_put_contents($file, $content);
}
echo "Replaced work_unit with unit_name";
