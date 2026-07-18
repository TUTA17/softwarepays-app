<?php

namespace App\Modules\SoundMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Services\R2StorageService;
use App\Modules\SoundMeme\Services\AudioMetadataService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlMyinstants extends Command
{
    protected $signature = 'soundmeme:crawl';
    protected $description = 'Crawl new sounds from myinstants.com';

    public function handle(R2StorageService $r2, AudioMetadataService $metadata)
    {
        $this->info('Starting crawl from myinstants...');
        
        $response = Http::get('https://www.myinstants.com/en/search/?name=meme');
        if (!$response->successful()) {
            $this->error('Failed to fetch myinstants');
            return;
        }

        $html = $response->body();
        preg_match_all('/<div class="instant">.*?<div class="small-button" onmousedown="play\([^,]+,\s*\'([^\']+)\'\)".*?<a href="[^"]+" class="instant-link">(.*?)<\/a>/s', $html, $matches, PREG_SET_ORDER);
        
        $added = 0;
        foreach ($matches as $match) {
            if ($added >= 10) break; // Lấy 10 bài
            
            $urlPath = $match[1];
            $title = trim($match[2]);
            $mp3Url = 'https://www.myinstants.com' . $urlPath;
            
            // Skip nếu trùng title
            if (Sound::where('title', $title)->exists()) {
                continue;
            }

            $this->info("Downloading: $title");
            
            $tempPath = tempnam(sys_get_temp_dir(), 'sound_');
            file_put_contents($tempPath, file_get_contents($mp3Url));
            
            try {
                $ext = 'mp3';
                $mime = 'audio/mpeg';
                $uuid = (string) Str::uuid();
                $key = 'sounds/meme/' . now()->format('Y/m') . '/' . $uuid . '.' . $ext;
                
                $metaInfo = $metadata->analyze($tempPath);
                $r2->uploadObject($tempPath, $key, $mime);
                
                Sound::create([
                    'title' => $title,
                    'slug' => Sound::generateUniqueSlug($title),
                    'object_key' => $key,
                    'original_filename' => basename($urlPath),
                    'mime_type' => $mime,
                    'extension' => $ext,
                    'duration' => $metaInfo['duration'] ?? 0,
                    'bitrate' => $metaInfo['bitrate'] ?? 0,
                    'codec' => $metaInfo['codec'] ?? 'mp3',
                    'file_size' => filesize($tempPath),
                    'status' => Sound::STATUS_DRAFT, // Vào hàng đợi duyệt
                ]);
                $added++;
                $this->info("Successfully added: $title");
            } catch (\Exception $e) {
                $this->error("Error processing $title: " . $e->getMessage());
                Log::error("Crawl error", ['e' => $e]);
            } finally {
                @unlink($tempPath);
            }
        }
        
        $this->info("Crawl completed. Added: $added");
    }
}
