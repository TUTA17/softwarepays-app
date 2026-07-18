<?php

namespace App\Modules\SoundMeme\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\Core\Models\Setting;

class AutoPublishSounds extends Command
{
    protected $signature = 'soundmeme:auto-publish';
    protected $description = 'Auto publish approved sounds based on settings';

    public function handle()
    {
        $rate = (int) Setting::getValue('soundmeme_autopublish_rate', 1);

        if ($rate <= 0) {
            $this->info('Auto publish is disabled (rate <= 0).');
            return;
        }

        $sounds = Sound::where('status', Sound::STATUS_APPROVED)
            ->orderBy('created_at', 'asc')
            ->take($rate)
            ->get();

        if ($sounds->isEmpty()) {
            $this->info('No approved sounds waiting to be published.');
            return;
        }

        foreach ($sounds as $sound) {
            $sound->update(['status' => Sound::STATUS_PUBLISHED]);
            $this->info("Published sound: {$sound->title}");
        }

        $this->info("Auto published {$sounds->count()} sounds.");
    }
}
