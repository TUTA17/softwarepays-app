<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Modules\Theme\Models\Product;
use Illuminate\Support\Facades\Cache;

$products = Product::all();
$updated = 0;
foreach ($products as $p) {
    if (isset($p->steam_data["genres"]) && is_array($p->steam_data["genres"]) && count($p->steam_data["genres"]) > 0) {
        $p->genres = json_encode($p->steam_data["genres"]);
        $p->save();
        $updated++;
    }
}

Cache::forget("shop_genres_list");
echo "Updated $updated products and cleared shop_genres_list cache.\n";

