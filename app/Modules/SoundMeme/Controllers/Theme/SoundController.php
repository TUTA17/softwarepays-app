<?php

namespace App\Modules\SoundMeme\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Models\SoundCategory;
use App\Modules\SoundMeme\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SoundController extends Controller
{
    public function __construct(protected R2StorageService $r2)
    {
    }

    public function index(Request $request)
    {
        $query = Sound::with('category')->where('status', Sound::STATUS_PUBLISHED);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        switch ($request->get('sort', 'newest')) {
            case 'popular':
                $query->orderBy('play_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('download_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $sounds = $query->paginate(12)->withQueryString();
        $sounds->getCollection()->transform(fn ($s) => $this->attachUrls($s));

        $categories = SoundCategory::where('status', true)->orderBy('name')->get();

        return view('soundmeme::theme.index', compact('sounds', 'categories'));
    }

    public function show($slug)
    {
        $sound = Sound::with('category')->where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();
        $sound = $this->attachUrls($sound);

        $related = Sound::where('status', Sound::STATUS_PUBLISHED)
            ->where('id', '!=', $sound->id)
            ->when($sound->category_id, fn ($q) => $q->where('category_id', $sound->category_id))
            ->orderBy('play_count', 'desc')
            ->take(6)
            ->get()
            ->map(fn ($s) => $this->attachUrls($s));

        return view('soundmeme::theme.show', compact('sound', 'related'));
    }

    // Tăng lượt nghe — chỉ gọi từ frontend sau khi thực sự nghe đủ vài giây (không phải mỗi lần
    // render), và tự giới hạn thêm ở đây theo IP+sound trong 1 giờ để chống spam gọi API trực tiếp.
    public function play(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'sound_play_' . $sound->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $sound->increment('play_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        return response()->json(['success' => true, 'play_count' => $sound->play_count]);
    }

    public function download(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'sound_download_' . $sound->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $sound->increment('download_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        $filename = \Illuminate\Support\Str::slug($sound->title) . '.' . $sound->extension;
        $url = $this->r2->getSignedDownloadUrl($sound->object_key, 15, $filename);

        return redirect()->away($url);
    }

    protected function attachUrls(Sound $sound): Sound
    {
        $sound->play_url = $this->r2->getSignedDownloadUrl($sound->object_key, 30);
        $sound->thumbnail_url = $sound->thumbnail_key ? $this->r2->getSignedDownloadUrl($sound->thumbnail_key, 30) : null;
        return $sound;
    }
}
