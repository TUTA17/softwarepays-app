<?php

namespace App\Modules\GifMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\GifMeme\Models\GifCategory;
use App\Modules\GifMeme\Services\R2StorageService;
use App\Modules\Core\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlGifs extends Command
{
    protected $signature = 'gifmeme:crawl {--limit=10} {--all : Cào hết toàn bộ danh mục trong 1 lượt chạy}';
    protected $description = 'Crawl GIFs from Tenor across multiple meme categories';

    // Tenor không có trang /categories/ thật như myinstants, nhưng tìm kiếm theo từ khoá riêng
    // cho từng chủ đề cho ra kết quả khác nhau -> dùng làm "danh mục" tương tự Sound Meme, thay vì
    // chỉ 1 từ khoá "meme-gifs" cố định (trước đây luôn dồn hết vào đúng 1 danh mục "Meme").
    protected array $categories = [
        'meme', 'reaction', 'funny', 'anime', 'animal',
        'movie', 'sports', 'gaming', 'crying', 'dance',
        'dog', 'cat', 'happy', 'sad', 'angry', 'shocked',
        'confused', 'bored', 'excited', 'nervous', 'scared',
        'proud', 'embarrassed', 'awkward', 'facepalm', 'applause',
        'love', 'birthday', 'party', 'food', 'work',
        'monday', 'weekend', 'money', 'music', 'tiktok', 'kpop',
    ];

    public function handle(R2StorageService $r2)
    {
        $crawlAll = (bool) $this->option('all');

        if ($crawlAll) {
            $limit = PHP_INT_MAX;
            $this->info('Chế độ --all: cào toàn bộ ' . count($this->categories) . ' danh mục.');
        } else {
            $rateSetting = Setting::where('name', 'gifmeme_autocrawl_rate')->first();
            $limit = $rateSetting ? (int) $rateSetting->value : (int) $this->option('limit');

            if ($limit <= 0) {
                $this->info('Auto crawl is disabled (rate <= 0).');
                return;
            }

            $this->info("Bắt đầu lấy tối đa {$limit} GIF mới, tiếp tục từ danh mục lần cào trước...");
        }

        $catIndex = $crawlAll ? 0 : $this->loadCursor();

        $added = 0;
        $skipped = 0;
        $loops = 0;
        $total = count($this->categories);

        while ($loops < $total && $added < $limit) {
            $catSlug = $this->categories[$catIndex % $total];
            $category = GifCategory::firstOrCreate(['slug' => $catSlug], ['name' => ucfirst($catSlug)]);

            $this->info("Đang quét danh mục: {$catSlug}...");
            sleep(1); // tôn trọng server Tenor, tránh gửi request dồn dập

            $url = "https://tenor.com/search/{$catSlug}-gifs";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->timeout(15)->get($url);

            if (!$response->successful()) {
                $this->error("Không thể tải trang Tenor cho danh mục {$catSlug}, bỏ qua.");
            } else {
                preg_match_all('/<img[^>]*src="([^"]+media\.tenor\.com[^"]+\.gif)"[^>]*alt="([^"]+)"[^>]*>/i', $response->body(), $matches, PREG_SET_ORDER);

                if (empty($matches)) {
                    $this->info("Không tìm thấy GIF nào ở danh mục {$catSlug}.");
                }

                foreach ($matches as $match) {
                    if ($added >= $limit) break;

                    $gifUrl = $match[1];
                    $title = trim(html_entity_decode($match[2]));

                    // Xóa chữ 'GIF' ở cuối tên nếu có
                    if (str_ends_with(strtolower($title), ' gif')) {
                        $title = substr($title, 0, -4);
                    }

                    if (empty($title)) {
                        $title = ucfirst($catSlug) . ' GIF ' . Str::random(5);
                    }

                    if (Gif::where('title', $title)->exists()) {
                        $skipped++;
                        continue;
                    }

                    if ($this->addGif($r2, $category->id, $catSlug, $title, $gifUrl)) {
                        $added++;
                    }

                    // Nghỉ 1-2s giữa các lần tải để tránh bị chặn
                    sleep(rand(1, 2));
                }
            }

            $catIndex++;
            $loops++;
        }

        if ($skipped > 0) {
            $this->info("Đã bỏ qua {$skipped} GIF vì đã có sẵn trong hệ thống.");
        }

        // Lưu vị trí danh mục để lần cào tiếp theo (không --all) tiếp tục thay vì luôn cào lại
        // đúng 1 danh mục đầu tiên.
        if (!$crawlAll) {
            $this->saveCursor($catIndex % $total);
        }

        $this->info("Crawl completed. Added: {$added}");
    }

    protected function loadCursor(): int
    {
        return (int) (Setting::where('name', 'gifmeme_crawl_cursor')->value('value') ?? 0);
    }

    protected function saveCursor(int $catIndex): void
    {
        Setting::updateOrCreate(
            ['name' => 'gifmeme_crawl_cursor'],
            ['value' => $catIndex, 'type' => 'gifmeme']
        );
    }

    protected function addGif(R2StorageService $r2, int $categoryId, string $catSlug, string $title, string $gifUrl): bool
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
                "GIF siêu hài hước \"{$title}\" thuộc thể loại " . ucfirst($catSlug) . ". Lưu ngay để comment dạo nào!",
                "Meme GIF \"{$title}\" (" . ucfirst($catSlug) . ") chất lừ. Bấm tải về để đi troll bạn bè nhé.",
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
