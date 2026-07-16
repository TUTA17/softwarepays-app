<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = \App\Modules\Theme\Models\Product::where('name', 'like', '%Jumping Master%')->first();
if ($p) {
    echo "Game: {$p->name}\n";
    echo "ID: {$p->wholesale_product_id}\n";
    echo "Keys: {$p->available_keys}\n";
} else {
    echo "Not found\n";
}
