<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class FetchGameNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lấy tin tức game tự động từ RSS (GameK) bao gồm cả nội dung chi tiết';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);

        // Thay đổi nguồn cấp RSS sang GameHub
        $rssUrl = 'https://gamehub.vn/portal/index.rss';
        $this->info("Bắt đầu lấy tin tức từ GameHub...");

        try {
            $response = Http::timeout(30)->withOptions(['verify' => false])->get($rssUrl);
            
            if (!$response->successful()) {
                $this->error("Không thể tải RSS. Mã lỗi: " . $response->status());
                return;
            }

            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (!$xml || !isset($xml->channel->item)) {
                $this->error("Định dạng RSS không hợp lệ.");
                return;
            }

            // Tự động tạo danh mục Tin Tức Game nếu chưa có
            $category = BlogCategory::firstOrCreate(
                ['slug' => 'tin-tuc-game'],
                ['name' => 'Tin Tức Game', 'description' => 'Tin tức game cập nhật tự động']
            );

            $count = 0;
            foreach ($xml->channel->item as $item) {
                $title = (string) $item->title;
                $link = (string) $item->link;
                $pubDate = Carbon::parse((string) $item->pubDate);
                $slug = Str::slug($title);

                // Nếu bài viết đã tồn tại thì bỏ qua
                if (BlogPost::where('slug', $slug)->exists()) {
                    continue;
                }

                // Cào nội dung chi tiết từ Link thật
                $this->info("Đang lấy nội dung: " . $title);
                $articleContent = $this->scrapeContent($link);
                
                if (!$articleContent) {
                    $this->warn("Bỏ qua bài viết hoặc không thể lấy nội dung: $title");
                    continue;
                }

                $summary = strip_tags((string) $item->description);
                $summary = Str::limit($summary, 200);

                // Lấy ảnh thumbnail từ RSS enclosure hoặc description
                $image = null;
                if (isset($item->enclosure) && isset($item->enclosure['url'])) {
                    $image = (string) $item->enclosure['url'];
                } else {
                    // GameHub dùng nháy đơn cho thuộc tính src: src='...'
                    preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', (string) $item->description, $matches);
                    if (isset($matches[1])) {
                        $image = $matches[1];
                    }
                }

                // Nếu trong RSS không có ảnh, lấy tạm ảnh đầu tiên trong bài viết
                if (!$image) {
                    preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $articleContent, $matches);
                    if (isset($matches[1])) {
                        $image = $matches[1];
                    }
                }
                
                // Nếu ảnh là dạng URL tương đối, thêm tên miền vào
                if ($image && strpos($image, 'http') === false) {
                    $image = 'https://gamehub.vn/' . ltrim($image, '/');
                }

                // Default nếu thật sự không có ảnh nào
                if (!$image) {
                    $image = 'https://via.placeholder.com/800x450.png?text=Game+News';
                }

                BlogPost::create([
                    'category_id' => $category->id,
                    'title' => $title,
                    'slug' => $slug,
                    'summary' => $summary,
                    'content' => $articleContent,
                    'image' => $image,
                    'source' => $link,
                    'author' => 'GameHub Bot',
                    'is_auto' => true,
                    'status' => true,
                    'pub_date' => $pubDate,
                ]);

                $count++;
            }

            $this->info("Đã đồng bộ xong $count bài viết mới với nội dung chi tiết!");
            
            // Thêm log để theo dõi lịch sử chạy
            $totalNews = BlogPost::count();
            \Illuminate\Support\Facades\Log::info("CronJob News Sync: Chạy hoàn tất. Đã thêm mới {$count} bài viết từ GameHub. Tổng số bài viết hiện tại: {$totalNews} bài.");

        } catch (\Exception $e) {
            $this->error("Có lỗi xảy ra: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("CronJob News Sync Lỗi: " . $e->getMessage());
        }
    }

    /**
     * Hàm cào nội dung chi tiết từ trang GameHub
     */
    private function scrapeContent($url)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ])->get($url);

            if (!$response->successful() || empty($response->body())) {
                return false;
            }

            $html = $response->body();

            // TÁCH NỘI DUNG CHÍNH (Đặc thù HTML của GameHub)
            if (preg_match('/<blockquote[^>]*class="[^"]*messageText[^"]*"[^>]*>(.*?)<\/blockquote>/is', $html, $matches)) {
                $content = $matches[1];
            } elseif (preg_match('/<article[^>]*>(.*?)<\/article>/is', $html, $matches)) {
                $content = $matches[1];
            } else {
                return false;
            }

            // DỌN DẸP HTML
            $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
            $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
            $content = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $content); // Gỡ thẻ link
            $content = str_replace('<img ', '<img style="max-width:100%; height:auto; display:block; margin: 15px auto; border-radius: 8px;" ', $content);

            return trim($content);

        } catch (\Exception $e) {
            return false;
        }
    }
}
