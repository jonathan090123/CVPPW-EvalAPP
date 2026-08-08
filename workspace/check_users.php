<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employees = App\Models\Employee::where('position', '!=', 'STAFF')->get();
foreach ($employees as $employee) {
    $first = strtolower(explode(' ', trim($employee->name))[0]);
    $user = App\Models\User::where('username', $first)->first();
    echo $employee->name . ' | ' . $employee->position . ' | ' . ($user ? $user->username : 'NO_USER') . PHP_EOL;
}
