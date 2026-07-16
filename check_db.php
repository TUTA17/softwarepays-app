<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Illuminate\Support\Facades\DB::select(urldecode('SHOW%20COLUMNS%20FROM%20transactions%20LIKE%20%27type%27'));
echo json_encode($columns);
