<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Shop\Controllers\Theme\ShopController;
use App\Modules\Shop\Controllers\Theme\ProductController;
use App\Modules\Shop\Controllers\Theme\CartController;
use App\Modules\Shop\Controllers\Theme\PurchaseController;
use App\Modules\Shop\Controllers\Theme\KinguinWebhookController;
use App\Modules\Shop\Controllers\Theme\CouponController;
use App\Modules\Shop\Controllers\Theme\CatalogController;

Route::middleware(['web'])->group(function () {
    Route::get('/system/run-queue/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }

        ignore_user_abort(true);
        set_time_limit(0);

        \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
        return response()->json(['status' => 'success', 'message' => 'Queue processed.']);
    });

    Route::post('/queue/work', function () {
        \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
        return response()->json(['status' => 'success', 'message' => 'Queue processed.']);
    });

    // Link chạy đồng bộ danh mục tự động qua Cron-job (GET request)
    Route::get('/system/sync-categories/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }

        ignore_user_abort(true);
        set_time_limit(0);

        $controller = new \App\Modules\Shop\Controllers\Admin\CategoryController();
        $controller->sync();

        return response()->json(['success' => true, 'message' => 'Đồng bộ danh mục hoàn tất']);
    });

    // Link chạy đồng bộ tin tức tự động qua Cron-job (GET request) — trỏ tới đây mỗi 3 tiếng.
    // news:fetch tự giới hạn 4 bài/lần nên không cần ignore_user_abort/set_time_limit dài như các
    // job nặng khác, nhưng vẫn giữ để nhất quán với các route /system/sync-* còn lại.
    Route::get('/system/sync-news/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }

        ignore_user_abort(true);
        set_time_limit(0);

        \Illuminate\Support\Facades\Artisan::call('news:fetch');

        return response()->json(['success' => true, 'message' => trim(\Illuminate\Support\Facades\Artisan::output())]);
    });

    // Link chạy đồng bộ kho tự động qua Cron-job (GET request)
    Route::get('/system/sync-stock/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }

        ignore_user_abort(true);
        set_time_limit(0);

        // Giới hạn 20 sản phẩm mỗi lần chạy để tránh lỗi 503 Timeout
        $products = \App\Modules\Theme\Models\Product::whereNotNull('wholesale_product_id')
            ->where('wholesale_product_id', 'not like', 'kinguin_mock_%')
            ->orderBy('updated_at', 'asc')
            ->limit(20)
            ->get();

        $providerService = new \App\Services\WholesaleProviderService();
        $updated = 0;
        $hidden = 0;
        $shown = 0;

        foreach ($products as $p) {
            // Đánh dấu đã kiểm tra bằng cách update timestamp
            $p->touch();

            // Luôn hiển thị nếu kho nội bộ còn Key
            $localKeysCount = $p->keys()->where('status', 'available')->count();
            if ($localKeysCount > 0) {
                if (!$p->is_active) {
                    $p->is_active = 1;
                    $p->save();
                    $shown++;
                    $updated++;
                }
                continue;
            }

            $idToCheck = $p->wholesale_product_id;
            if (!$idToCheck) continue;

            $inStock = $providerService->checkStock($idToCheck);

            if ($inStock && !$p->is_active) {
                $p->is_active = 1;
                $p->save();
                $shown++;
                $updated++;
            } elseif (!$inStock && $p->is_active) {
                $p->is_active = 0;
                $p->save();
                $hidden++;
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đồng bộ kho hoàn tất. $updated sản phẩm được cập nhật ($shown hiển thị lại, $hidden bị ẩn)."
        ]);
    });

    // Link chạy đồng bộ giá tự động qua Cron-job (GET request)
    Route::get('/system/sync-prices/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }
        
        ignore_user_abort(true);
        set_time_limit(0);

        // Giới hạn 20 sản phẩm mỗi lần chạy để tránh lỗi 503 Timeout
        $products = \App\Modules\Theme\Models\Product::whereNotNull('wholesale_product_id')
            ->where('wholesale_product_id', 'not like', 'kinguin_mock_%')
            ->orderBy('updated_at', 'asc')
            ->limit(20)
            ->get();
            
        $providerService = new \App\Services\WholesaleProviderService();
        $updated = 0;

        foreach ($products as $p) {
            // Đánh dấu đã kiểm tra bằng cách update timestamp
            $p->touch();

            $idToCheck = $p->wholesale_product_id;
            if (!$idToCheck) continue;

            $prices = $providerService->getWholesalePrice($idToCheck);
            
            if (isset($prices['selling_price']) && $prices['selling_price'] > 0) {
                $p->price = $prices['selling_price'];
                $p->original_price = $prices['original_price'];
                $p->save();
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đồng bộ giá hoàn tất. $updated sản phẩm đã được cập nhật giá theo thị trường Kinguin."
        ]);
    });

    // Link chạy tự động ghép nối (Auto-Map) mã Kinguin Product ID (GET request)
    Route::get('/system/auto-map/{token}', function ($token) {
        if ($token !== 'K9xP2mQvL5') {
            abort(403);
        }
        
        ignore_user_abort(true);
        set_time_limit(0);

        // Lấy 20 sản phẩm chưa có mã Kinguin ID
        $products = \App\Modules\Theme\Models\Product::whereNull('wholesale_product_id')
            ->whereNotNull('steam_app_id')
            ->limit(20)
            ->get();
            
        if ($products->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tuyệt vời! Tất cả sản phẩm đều đã được ghép nối mã Kinguin.'
            ]);
        }

        $apiUrl = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_endpoint')->value('value');
        $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
        
        if (!$apiUrl || !$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: Chưa cấu hình API Endpoint hoặc API Key của Kinguin trong Admin.'
            ]);
        }
        
        $apiUrl = rtrim($apiUrl, '/');
        $mapped = 0;
        $failed = 0;

        foreach ($products as $p) {
            // Encode tên game để tìm kiếm chuẩn xác
            $searchName = trim($p->name);
            
            // Tìm kiếm game trên Kinguin
            $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-Api-Key' => $apiKey
            ])->get($apiUrl . '/api/v1/products', [
                'name' => $searchName,
                'limit' => 5
            ]);
            
            if ($response->successful()) {
                $results = $response->json('results') ?? $response->json('items');
                // Kinguin API might return products at the root or under 'results'/'items'
                if (!$results && is_array($response->json()) && isset($response->json()[0]['productId'])) {
                    $results = $response->json();
                }

                if (!empty($results) && isset($results[0]['productId'])) {
                    // Lấy kết quả đầu tiên (khớp nhất)
                    $p->wholesale_product_id = $results[0]['productId'];
                    $p->save();
                    $mapped++;
                } else {
                    \Illuminate\Support\Facades\Log::warning('Kinguin Auto-Map No Match', [
                        'game_name' => $searchName,
                        'response' => $response->body()
                    ]);
                    // Tạm đánh dấu để không lặp lại vô tận (nếu muốn)
                    // $p->touch();
                    $failed++;
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Kinguin Auto-Map API Error', [
                    'game_name' => $searchName,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $failed++;
            }
            
            // Đánh dấu đã quét để tránh lặp lại cùng 20 game nếu thất bại liên tục
            $p->touch();
        }

        return response()->json([
            'success' => true,
            'message' => "Auto-Map hoàn tất vòng này. Thành công: $mapped game. Không tìm thấy trên Kinguin: $failed game. Hãy F5 lại link này để map tiếp 20 game khác!"
        ]);
    });

    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::get('/steam-wallet', [ShopController::class, 'steamWallet'])->name('steam-wallet');
    Route::get('/api/search', [ShopController::class, 'searchApi'])->name('api.search');
    Route::get('/game/{id}-{slug}', [ProductController::class, 'show'])->name('product.show');

    // Danh mục: eSIM, Thẻ nạp/thẻ game, Gói đăng ký, Phần mềm, Thẻ quà tặng
    Route::get('/esim-du-lich', [CatalogController::class, 'browseEsim'])->name('catalog.esim');
    Route::get('/esim-du-lich/{id}', [CatalogController::class, 'showEsim'])->name('catalog.esim.show');
    Route::get('/the-nap-va-the-game', [CatalogController::class, 'browseCard'])->name('catalog.card');
    Route::get('/the-nap-va-the-game/{id}', [CatalogController::class, 'showCard'])->name('catalog.card.show');
    Route::get('/danh-muc/{slug}', [CatalogController::class, 'browseSimple'])->name('catalog.simple');

    // Public Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/save/{id}', [CouponController::class, 'save'])->name('coupons.save');

    Route::middleware('auth')->group(function () {
        // My Coupons
        Route::get('/my-coupons', [CouponController::class, 'myCoupons'])->name('coupons.my');

        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
        Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

        // Checkout
        Route::get('/cart/checkout', [CartController::class, 'checkoutView'])->name('cart.checkout');
        Route::post('/cart/checkout', [CartController::class, 'checkoutProcess'])->name('cart.checkout.process');
        Route::get('/cart/checkout/verify', [CartController::class, 'checkoutVerifyForm'])->name('cart.checkout.verify');
        Route::post('/cart/checkout/verify', [CartController::class, 'checkoutVerifyProcess'])->name('cart.checkout.verify.post');

        // Purchase (Buy Now)
        Route::post('/game/{id}/buy', [PurchaseController::class, 'buy'])->name('product.buy');
    });

    // Kinguin Webhook Routes
    Route::post('/api/kinguin/webhook/product-update', [KinguinWebhookController::class, 'onProductUpdate']);
    Route::post('/api/kinguin/webhook/order-completed', [KinguinWebhookController::class, 'onOrderCompleted']);
    Route::post('/api/kinguin/webhook/status-change', [KinguinWebhookController::class, 'onOrderStatusChange']);
});
