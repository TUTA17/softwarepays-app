<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiService = new App\Services\SmmApi();
$services = $apiService->getServices();
if (!empty($services)) {
    echo json_encode(array_slice($services, 0, 10), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "No services returned";
}
