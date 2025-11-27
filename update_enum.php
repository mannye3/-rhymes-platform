<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Update the enum values
    DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
    echo "Successfully updated the area enum values in rev_sync_logs table.\n";
} catch (Exception $e) {
    echo "Error updating enum values: " . $e->getMessage() . "\n";
}