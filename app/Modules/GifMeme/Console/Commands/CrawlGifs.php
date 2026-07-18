<?php

namespace App\Modules\GifMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\GifMeme\Models\GifCategory;
use App\Modules\GifMeme\Services\R2StorageService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlGifs extends Command
{
    protected $signature = 'gifmeme:crawl {--limit=10} {--query=meme-gifs}';
    protected $description = 'Crawl GIFs from Tenor (Meme Search)';

    public function handle(R2StorageService $r2)
    {
        $limit = (int) $this->option('limit');
        $query = (string) $this->option('query');

        $this->info("Bắt đầu lấy tối đa {$limit} GIF mới từ Tenor (query: {$query})...");

        $category = GifCategory::firstOrCreate(['slug' => 'meme'], ['name' => 'Meme']);

        // Cào trang Tenor search meme
        $url = "https://tenor.com/search/" . $query;
        
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ])->get($url);

        if (!$response->successful()) {
            $this->error("Không thể tải trang Tenor.");
            return;
        }

        // Regex tìm các ảnh GIF trong HTML của Tenor
        // Tenor dùng thẻ img với src chứa media.tenor.com và alt là tiêu đề
        preg_match_all('/<img[^>]*src="([^"]+media\.tenor\.com[^"]+\.gif)"[^>]*alt="([^"]+)"[^>]*>/i', $response->body(), $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            $this->info("Không tìm thấy GIF nào trên trang.");
            return;
        }

        $added = 0;
        $skipped = 0;

        foreach ($matches as $match) {
            if ($added >= $limit) break;

            $gifUrl = $match[1];
            $title = trim(html_entity_decode($match[2]));
            
            // Xóa chữ 'GIF' ở cuối tên nếu có
            if (str_ends_with(strtolower($title), ' gif')) {
                $title = substr($title, 0, -4);
            }

            if (empty($title)) {
                $title = 'Meme GIF ' . Str::random(5);
            }

            if (Gif::where('title', $title)->exists()) {
                $skipped++;
                continue;
            }

            if ($this->addGif($r2, $category->id, $title, $gifUrl)) {
                $added++;
            }
            
            // Nghỉ 1-2s giữa các lần tải để tránh bị chặn
            sleep(rand(1, 2));
        }

        if ($skipped > 0) {
            $this->info("Đã bỏ qua {$skipped} GIF vì đã có sẵn trong hệ thống.");
        }

        $this->info("Crawl completed. Added: {$added}");
    }

    protected function addGif(R2StorageService $r2, int $categoryId, string $title, string $gifUrl): bool
    {
        $this->info("Downloading: {$title}");

        $tempPath = tempnam(sys_get_temp_dir(), 'gif_');
        
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ])->get($gifUrl);

        if (!$response->successful()) {
            $this->error("Không tải được file GIF, bỏ qua: {$title}");
            @unlink($tempPath);
            return false;
        }

        file_put_contents($tempPath, $response->body());

        try {
            $ext = 'gif';
            $mime = 'image/gif';
            $uuid = (string) Str::uuid();
            $key = 'gifs/meme/' . now()->format('Y/m') . '/' . $uuid . '.' . $ext;

            // Optional: get dimensions
            $width = null;
            $height = null;
            $size = getimagesize($tempPath);
            if ($size) {
                $width = $size[0];
                $height = $size[1];
            }

            $r2->uploadObject($tempPath, $key, $mime);

            $descriptions = [
                "GIF siêu hài hước \"{$title}\". Lưu ngay để comment dạo nào!",
                "Meme GIF \"{$title}\" chất lừ. Bấm tải về để đi troll bạn bè nhé.",
            ];

            Gif::create([
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(4),
                'description' => $descriptions[array_rand($descriptions)],
                'object_key' => $key,
                'original_filename' => basename(parse_url($gifUrl, PHP_URL_PATH)),
                'mime_type' => $mime,
                'extension' => $ext,
                'width' => $width,
                'height' => $height,
                'file_size' => filesize($tempPath),
                'status' => Gif::STATUS_PUBLISHED, // Publish luôn cho tiện
            ]);
            $this->info("Successfully added: {$title}");
            return true;
        } catch (\Exception $e) {
            $this->error("Error processing {$title}: " . $e->getMessage());
            Log::error('Crawl error', ['e' => $e]);
            return false;
        } finally {
            @unlink($tempPath);
        }
    }
}
