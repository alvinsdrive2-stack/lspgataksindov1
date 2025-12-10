<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ROLES TABLE ===\n";
$roles = \App\Models\Role::all();
foreach ($roles as $role) {
    echo "ID: {$role->id}, Role: {$role->role}\n";
}

echo "\n=== USERS TABLE ===\n";
$users = \App\Models\User::select('id', 'name', 'role')->take(10)->get();
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Role: {$user->role}\n";
}

echo "\n=== CHECK ROLE VALIDATION ===\n";
$allRoles = \App\Models\Role::pluck('role')->toArray();
echo "Valid roles from database: " . implode(', ', $allRoles) . "\n";

$userRoles = \App\Models\User::distinct()->pluck('role')->toArray();
echo "Roles used by users: " . implode(', ', $userRoles) . "\n";

foreach ($userRoles as $role) {
    if (!in_array($role, $allRoles)) {
        echo "⚠️ Role '$role' exists in users but NOT in roles table!\n";
    }
}