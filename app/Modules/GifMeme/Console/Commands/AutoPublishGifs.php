<?php

namespace App\Modules\GifMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\Core\Models\Setting;

class AutoPublishGifs extends Command
{
    protected $signature = 'GifMeme:auto-publish';
    protected $description = 'Auto publish approved Gifs based on settings';

    public function handle()
    {
        $rateSetting = Setting::where('name', 'GifMeme_autopublish_rate')->first();
        $rate = $rateSetting ? (int) $rateSetting->value : 1;

        if ($rate <= 0) {
            $this->info('Auto publish is disabled (rate <= 0).');
            return;
        }

        $Gifs = Gif::where('status', Gif::STATUS_APPROVED)
            ->orderBy('created_at', 'asc')
            ->take($rate)
            ->get();

        if ($Gifs->isEmpty()) {
            $this->info('No approved Gifs waiting to be published.');
            return;
        }

        foreach ($Gifs as $Gif) {
            $Gif->update(['status' => Gif::STATUS_PUBLISHED]);
            $this->info("Published Gif: {$Gif->title}");
        }

        $this->info("Auto published {$Gifs->count()} Gifs.");
    }
}


