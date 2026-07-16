<?php

namespace App\Jobs;

use App\Modules\Theme\Models\Category;
use App\Modules\Theme\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

// Chạy nền qua queue vì bảng products hiện có ~97k dòng (hàng Kinguin) — chạy đồng bộ trong 1 request
// HTTP như code cũ chắc chắn vượt quá max_execution_time/memory_limit và không bao giờ hoàn tất được,
// khiến danh mục không bao giờ được cập nhật đúng theo dữ liệu Kinguin (tiếng Anh) thật.
class SyncCategoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;

    public function handle(): void
    {
        $categoryMap = [];

        Product::whereNotNull('genres')->select('id', 'genres')->chunkById(500, function ($products) use (&$categoryMap) {
            foreach ($products as $product) {
                if (!$product->genres) continue;

                $genres = is_array($product->genres) ? $product->genres : json_decode($product->genres, true);
                if (!is_array($genres)) continue;

                $catIds = [];
                foreach ($genres as $genreName) {
                    $genreName = trim((string) $genreName);
                    if ($genreName === '') continue;

                    $slug = Str::slug($genreName);
                    if ($slug === '') $slug = md5($genreName);

                    if (!isset($categoryMap[$slug])) {
                        $cat = Category::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $genreName, 'is_active' => true]
                        );
                        $categoryMap[$slug] = $cat->id;
                    }

                    $catIds[] = $categoryMap[$slug];
                }

                // sync() (không phải syncWithoutDetaching) để tự gỡ liên kết thể loại cũ không còn đúng
                // với genres hiện tại của sản phẩm — tránh tồn đọng dữ liệu rác qua nhiều lần đồng bộ.
                $product->categories()->sync($catIds);
            }
        });

        // Dọn các danh mục cũ (thời Steam, tiếng Việt) không còn sản phẩm Kinguin nào khớp tên nữa.
        Category::whereDoesntHave('products')->delete();
    }
}
