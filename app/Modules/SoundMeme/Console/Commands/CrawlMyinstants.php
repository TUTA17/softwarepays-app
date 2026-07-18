<?php

namespace App\Modules\SoundMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Services\R2StorageService;
use App\Modules\SoundMeme\Services\AudioMetadataService;
use App\Modules\SoundMeme\Models\SoundCategory;
use App\Modules\Core\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlMyinstants extends Command
{
    protected $signature = 'soundmeme:crawl {--limit=10} {--all : Cào hết toàn bộ danh mục, không giới hạn số lượng}';
    protected $description = 'Crawl sounds from myinstants.com (toàn bộ danh mục, phân trang tới khi hết)';

    // 10 danh mục thật của myinstants.com (dò trực tiếp từ menu trang chủ) — crawl theo từng
    // danh mục + phân trang (?page=N tới khi hết) thay vì chỉ 1 từ khoá tìm kiếm cố định như
    // bản cũ, để phủ được toàn bộ dữ liệu trang thay vì chỉ 1 trang kết quả giới hạn.
    protected array $categories = [
        'memes', 'reactions', 'games', 'movies', 'music',
        'politics', 'pranks', 'sports', 'television', 'viral',
    ];

    protected string $pattern = '/<div class="instant">.*?<button class="small-button" onclick="play\(\'([^\']+)\'[^)]+\)".*?<a href="([^"]+)" class="instant-link[^"]*">(.*?)<\/a>/s';

    public function handle(R2StorageService $r2, AudioMetadataService $metadata)
    {
        $crawlAll = (bool) $this->option('all');

        if ($crawlAll) {
            $limit = PHP_INT_MAX;
            $this->info('Chế độ --all: cào toàn bộ ' . count($this->categories) . ' danh mục, phân trang tới khi hết (có thể mất nhiều thời gian).');
        } else {
            $rateSetting = Setting::where('name', 'soundmeme_autocrawl_rate')->first();
            $limit = $rateSetting ? (int) $rateSetting->value : (int) $this->option('limit');

            if ($limit <= 0) {
                $this->info('Auto crawl is disabled (rate <= 0).');
                return;
            }

            $this->info("Bắt đầu lấy tối đa {$limit} bài mới, tiếp tục từ vị trí lần cào trước...");
        }

        // Con trỏ (cursor) lưu vị trí danh mục/trang đã cào tới lần trước, để lần cào bình thường
        // (không --all) không lặp lại mãi trang 1 của cùng 1 danh mục mà tiến dần qua toàn bộ site
        // theo thời gian. Chế độ --all luôn quét lại từ đầu, không dùng cursor.
        $cursor = $crawlAll ? ['cat' => 0, 'page' => 1] : $this->loadCursor();

        $added = 0;
        $catIndex = $cursor['cat'];
        $startPage = $cursor['page'];

        while ($catIndex < count($this->categories) && $added < $limit) {
            $catSlug = $this->categories[$catIndex];
            $category = SoundCategory::firstOrCreate(['slug' => $catSlug], ['name' => ucfirst($catSlug)]);

            $page = $startPage;
            $startPage = 1; // chỉ danh mục đầu tiên (nơi tiếp tục) mới dùng trang đã lưu, còn lại luôn từ trang 1

            while ($added < $limit) {
                $url = "https://www.myinstants.com/en/categories/{$catSlug}/lt/?page={$page}";
                $this->info("Đang quét: {$catSlug} (trang {$page})...");

                sleep(1); // tôn trọng server myinstants, tránh gửi request dồn dập

                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])->timeout(15)->get($url);

                if (!$response->successful()) {
                    $this->info("Hết trang ở danh mục {$catSlug} (trang {$page} trả về lỗi).");
                    break; // hết phân trang của danh mục này -> qua danh mục kế tiếp
                }

                preg_match_all($this->pattern, $response->body(), $matches, PREG_SET_ORDER);
                if (empty($matches)) {
                    $this->info("Không còn dữ liệu ở danh mục {$catSlug} (trang {$page}).");
                    break;
                }

                foreach ($matches as $match) {
                    if ($added >= $limit) break;

                    $urlPath = $match[1];
                    $title = trim(html_entity_decode($match[3]));
                    $mp3Url = 'https://www.myinstants.com' . $urlPath;

                    if (Sound::where('title', $title)->exists()) {
                        continue;
                    }

                    $this->addSound($r2, $metadata, $category->id, $catSlug, $title, $urlPath, $mp3Url);
                    $added++;
                }

                $page++;
            }

            $catIndex++;
        }

        // Lưu lại vị trí để lần cào tiếp theo (không --all) tiếp tục thay vì cào lại từ đầu.
        // Nếu đã quét hết toàn bộ danh mục, quay vòng lại từ đầu (cat=0, page=1).
        if (!$crawlAll) {
            $this->saveCursor($catIndex >= count($this->categories) ? 0 : $catIndex, 1);
        }

        $this->info("Crawl completed. Added: {$added}");
    }

    protected function loadCursor(): array
    {
        $raw = Setting::where('name', 'soundmeme_crawl_cursor')->value('value');
        $decoded = $raw ? json_decode($raw, true) : null;

        return [
            'cat' => $decoded['cat'] ?? 0,
            'page' => $decoded['page'] ?? 1,
        ];
    }

    protected function saveCursor(int $catIndex, int $page): void
    {
        Setting::updateOrCreate(
            ['name' => 'soundmeme_crawl_cursor'],
            ['value' => json_encode(['cat' => $catIndex, 'page' => $page]), 'type' => 'soundmeme']
        );
    }

    protected function addSound(R2StorageService $r2, AudioMetadataService $metadata, int $categoryId, string $catSlug, string $title, string $urlPath, string $mp3Url): void
    {
        $this->info("Downloading: {$title}");

        $tempPath = tempnam(sys_get_temp_dir(), 'sound_');
        $audioContent = @file_get_contents($mp3Url);

        if (!$audioContent) {
            $this->error("Không tải được file audio, bỏ qua: {$title}");
            @unlink($tempPath);
            return;
        }

        file_put_contents($tempPath, $audioContent);

        try {
            $ext = 'mp3';
            $mime = 'audio/mpeg';
            $uuid = (string) Str::uuid();
            $key = 'sounds/meme/' . now()->format('Y/m') . '/' . $uuid . '.' . $ext;

            $metaInfo = $metadata->analyze($tempPath);
            $r2->uploadObject($tempPath, $key, $mime);

            $thumbnailKey = $this->generatePlaceholderThumbnail($r2, $title);

            $descriptions = [
                "Bạn đang nghe âm thanh \"{$title}\" thuộc thể loại " . ucfirst($catSlug) . ". Nhấn tải xuống ngay để lưu lại âm thanh vui nhộn này nhé!",
                "Khám phá ngay âm thanh \"{$title}\" cực kỳ hài hước trong danh mục " . ucfirst($catSlug) . ". Đừng quên chia sẻ cho bạn bè!",
                "Âm thanh meme \"{$title}\" siêu hot đang làm mưa làm gió. Bấm nghe thử và tải về miễn phí ngay hôm nay.",
                "Cười té ghế với âm thanh \"{$title}\" (" . ucfirst($catSlug) . "). Sử dụng nó để troll bạn bè hoặc chèn vào video của bạn thì hết sảy!",
            ];

            Sound::create([
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => Sound::generateUniqueSlug($title),
                'description' => $descriptions[array_rand($descriptions)],
                'object_key' => $key,
                'thumbnail_key' => $thumbnailKey,
                'original_filename' => basename($urlPath),
                'mime_type' => $mime,
                'extension' => $ext,
                'duration' => $metaInfo['duration'] ?? 0,
                'bitrate' => $metaInfo['bitrate'] ?? 0,
                'codec' => $metaInfo['codec'] ?? 'mp3',
                'file_size' => filesize($tempPath),
                'status' => Sound::STATUS_DRAFT,
            ]);

            $this->info("Successfully added: {$title}");
        } catch (\Exception $e) {
            $this->error("Error processing {$title}: " . $e->getMessage());
            Log::error('Crawl error', ['e' => $e]);
        } finally {
            @unlink($tempPath);
        }
    }

    protected function generatePlaceholderThumbnail(R2StorageService $r2, string $title): ?string
    {
        $imgUrl = 'https://ui-avatars.com/api/?name=' . urlencode(mb_substr($title, 0, 2)) . '&background=random&color=fff&size=512&font-size=0.4';
        $imgContent = @file_get_contents($imgUrl);

        if (!$imgContent) {
            return null;
        }

        $imgTemp = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($imgTemp, $imgContent);

        try {
            $imgName = 'sounds/thumbnails/' . now()->format('Y/m') . '/' . Str::slug($title) . '_' . time() . '.png';
            $r2->uploadObject($imgTemp, $imgName, 'image/png');
            return $imgName;
        } finally {
            @unlink($imgTemp);
        }
    }
}
