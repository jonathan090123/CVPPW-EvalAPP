<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employees = App\Models\Employee::where('position', '!=', 'STAFF')->get();
foreach ($employees as $employee) {
    $employee->syncUserAccount();
}

echo 'Synced ' . App\Models\Employee::where('position', '!=', 'STAFF')->count() . ' employees.' . PHP_EOL;
