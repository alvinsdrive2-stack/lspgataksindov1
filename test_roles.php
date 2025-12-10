<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get roles from database
$roles = \App\Models\Role::all();
echo "Roles in database:\n";
foreach ($roles as $role) {
    echo "- {$role->role}\n";
}

// Get users with roles
echo "\nUsers with roles:\n";
$users = \App\Models\User::take(5)->get();
foreach ($users as $user) {
    echo "- ID: {$user->id}, Name: {$user->name}, Role: {$user->role}\n";
}