<?php
// Simple script to check if routes are properly registered
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes();

echo "Checking for users.edit route:\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'users/{id}/edit') !== false) {
        echo "Found route: " . $route->uri() . "\n";
        echo "Methods: " . implode(', ', $route->methods()) . "\n";
        echo "Action: " . $route->getActionName() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
        break;
    }
}