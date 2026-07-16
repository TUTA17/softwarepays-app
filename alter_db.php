<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::statement("ALTER TABLE products ADD steam_data JSON NULL;");
echo "Column added successfully.";

